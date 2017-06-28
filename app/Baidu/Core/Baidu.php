<?php

namespace Baidu;

use App\Http\Controllers\CookieController;
use App\Traits\Order;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use MercurySeries\Flashy\Flashy;

class Baidu
{
    use Order;

    public $source;
    public $secret;
    protected $encrypt;
    protected $version;
    protected $cmd;
    protected $api_url;
    protected $timestamp;
    public $shop_id;
    private $curlopt;
    private $refere;

    public function __construct()
    {
        if (func_num_args() != 0 && count(func_get_arg(0)) == 2) {
            $this->setAuth(func_get_arg(0));
        } else {
            $this->setAuth();
        }

        $this->version()->encrypt()->client(bd_api_url());
    }

    public function authorized($baidu_shop_id)
    {
        $bindUrl = 'http://dev.waimai.baidu.com/dev/norm/shopapplybind';

        $user = config('baidutakeout.baidu.login');
        $auth = config('baidutakeout.baidu.authorized');
        $auth['wid'] = $baidu_shop_id;


        // 请求登录接口，获取登录后的 cookie，存在 storage_path('cookie.txt') 文件中
        $this->login($user);
        $this->setRequest($auth);

        $res = $this->execCurl($bindUrl);
        return $res;
    }

    public function openOrderPush($shop_id)
    {
        $option = $this->buildCmd('order.push.open', compact('shop_id'));

        return $this->send($option);
    }

    public function getShopInfo($shop_id)
    {
        $option = $this->buildCmd('shop.get', compact('shop_id'));

        return $this->send($option);
    }

    public function getShopResponse(array $response)
    {
        if ($response['body']['errno'] != 0) {
            $response['body']['timestamp'] = $response['timestamp'];
            unset($response['body']['data']);

            $response = array_only($response, 'body');

            return $response['body'];
        }

        $response['body']['timestamp'] = $response['timestamp'];
        $response['body']['data'] = array_only($response['body']['data'], ['shop_id', 'name', 'shop_logo']);
        $response = array_only($response, 'body');

        return $response['body'];
    }

    /**
     * 仅仅只是为了通过登录接口获取登录后的 cookie
     *
     * @param array $user 登录百度需要的开发者信息
     * @return $this
     */
    private function login($user)
    {
        $loginUrl = 'https://wmpass.baidu.com/api/login';
        $this->refere = 'https://wmpass.baidu.com/ucenter/userlogin?redirect_url=http://dev.waimai.baidu.com';
        $this->setRequest($user)->execCurl($loginUrl);
        return $this;
    }

    private function execCurl($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, $this->curlopt);
        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new \RuntimeException('登录百度开发者后台进行授权出错');
        }

        return $res;
    }

    public function setRequest($data)
    {
        $this->cookieJar = storage_path('cookie.txt');

        $this->curlopt = [
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_COOKIEJAR => $this->cookieJar,
            CURLOPT_COOKIEFILE => $this->cookieJar,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                'Refere' => $this->refere
            ],
        ];
        return $this;
    }

    public function setAuth($auth = [])
    {
        if ($auth) {
            return $this->source($auth[0])->secret_key($auth[1]);
        }
        return $this->source()->secret_key();
    }

    public function version()
    {
        if (func_num_args() == 1) {
            $this->secret = func_get_arg(0);
        } else {
            $this->version = 3;
        }
        return $this;
    }

    public function encrypt()
    {
        if (func_num_args() == 1) {
            $this->encrypt = func_get_arg(0);
        } else {
            $this->encrypt = '';
        }
        return $this;
    }

    public function cmd($cmd)
    {
        $this->cmd = $cmd;
        return $this;
    }

    protected function api_url()
    {
        if (func_num_args() == 1) {
            $this->api_url = func_get_arg(0);
        } else {
            $this->api_url = bd_api_url();
        }
        return $this->api_url;
    }

    protected function source()
    {
        if (func_num_args() == 1) {
            $this->source = func_get_arg(0);
        } else {
            $this->source = source();
        }
        return $this;
    }

    protected function secret_key()
    {
        if (func_num_args() == 1) {
            $this->secret = func_get_arg(0);
        } else {
            $this->secret = secret_key();
        }
        return $this;
    }

    /**
     * 生成请求
     *
     * @param string $cmd
     * @param array $body
     * @param int $encode json_encode 转码
     * @return mixed
     */
    public function buildCmd($cmd, array $body, $encode = 1)
    {
        $this->cmd($cmd);

        $req['cmd']       = $this->cmd;
        $req['source']    = $this->source;
        $req['secret']    = $this->secret;
        $req['ticket']    = uuid();
        $req['version']   = $this->version;
        $req['encrypt']   = $this->encrypt;
        $req['timestamp'] = time();
        $req['body']      = $body;
        $req['body']      = json_encode($req['body']);
        ksort($req);

        $req['sign']      = gen_baidu_sign($req);

        unset($req['secret']);
        $req['body']      = $encode ? $req['body'] : json_decode($req['body'], 1);
        return $req;
    }

    /**
     * 生成响应
     *
     * @param string $cmd
     * @param array $data
     * @param int $encode json_encode 转码
     * @param int $errno
     * @param string $error
     * @return mixed
     */
    public function buildRes($cmd, $data = array(), $encode = 1, $errno = 0, $error = 'success') {
        $body['errno'] = $errno;
        $body['error'] = $error;
        $body['data']  = $data;

        return $this->buildCmd($cmd, $body, $encode);
    }

    /**
     * 从缓存中取出店家在易联云的相关信息
     *
     * @param array|null $data
     * @param int $expire
     * @return mixed
     */
    public function shopInfoFromCache($data = null, $expire = 1440)
    {
        return \Cache::remember('bdwm:shop:' . $this->shop_id, $expire, function () use ($data){
            if (is_array($data)) {
                return $data;
            }

            $res = $this->send(['baidu_shop_id' => $data], bdwm_info_url());

            if ($res['status'] == 0) {
                return $res['data'];
            }

            return null;
        });
    }
}
