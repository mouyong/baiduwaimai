<?php

namespace Baidu;

use App\Traits\Order;
use App\Traits\KaTestShop;
use App\Traits\Dish;
use GuzzleHttp\Client;

class Baidu
{
    use Dish, Order, KaTestShop;

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
        if (func_num_args() == 1) {
            if (!empty(func_get_arg(0))) {
                $this->setAuth(func_get_arg(0)[0]);
            }
        }

        $this->version()->encrypt()->client(bd_api_url());
    }

    public function authorized($baidu_shop_id, $source = null)
    {
        $bindUrl = 'http://dev.waimai.baidu.com/dev/norm/shopapplybind';

        // 判断目录中是否有文件
        if (!file_exists(storage_path('cookie'))) {
            // 没有文件就读取配置信息
            $user = config('baidutakeout.baidu.login');
            // 并执行登录操作
            $this->login($user);
        }

        // 读取配置信息
        $auth = config('baidutakeout.baidu.authorized');
        $auth['wid'] = $baidu_shop_id;
        // 授权
        if (!$source) {
            $loadResult = $this->loadOneSource($auth, $baidu_shop_id);

            // ka 用户，但是当前 source 已经达到了 200 限制
            if ($loadResult === 'ka') {
                return [];
            }

            if (is_null($loadResult)) {
                return null;
            }

            if ($loadResult === false || $loadResult == '') {
                return false; // false
            }
        }
        // 取消授权
        else {
            $auth['bindapply_type'] = 2;
            $auth['auth_cmd_category'] = '';
        }

        // 请求登录接口，获取登录后的 cookie，存在 storage_path('cookie.txt') 文件中
        $this->setRequest($auth);

        // 发送 bind 请求
        $res = $this->execCurl($bindUrl);
        $res = json_decode($res, true);
        $res['source'] = $auth['source'];

        return $res;
    }

    public function loadOneSource(&$auth, $baidu_shop_id)
    {
        // 获取一个未满 200 的 source
        $client = new Client();
        $res = $client->get(no_upper_limit_source_info_url() . '?baidu_shop_id=' . $baidu_shop_id);
        // dd(no_upper_limit_source_info_url() . '?baidu_shop_id=' . $baidu_shop_id);

        $res = json_decode($res->getBody(), true);
        $data = $res['data'];

        switch ($res['status']) {
            case 0:
                // 填写配置信息的 商户 ID
                $auth['source'] = $data['source'];
                return true;
                break;
            case 203:
                if ($data['audit_state'] == 'in_audit') {
                    return null;
                } elseif ($data['audit_state'] == 'completed') {
                    $auth['source'] = $data['source'];
                    return true;
                }
                break;
            case 202:
                return $data['state'];
                break;
            default:
                return false;
                break;
        }
    }

    public function openOrderPush($baidu_shop_id, $source)
    {
        $option = $this->setAuth($source)->buildCmd('order.push.open', compact('baidu_shop_id'));

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

    public function createShop()
    {
        return $this->shopCreate();
    }

    public function createDish()
    {
        return $this->dishCreate();
    }

    /**
     * 仅仅只是为了通过登录接口获取登录后的 cookie
     *
     * @param array $user 登录百度需要的开发者信息
     * @return $this
     */
    private function login($user)
    {
        // 定义登录请求的 url
        $loginUrl = 'https://wmpass.baidu.com/api/login';
        // 定义来源地址
        $this->refere = 'https://wmpass.baidu.com/ucenter/userlogin?redirect_url=http://dev.waimai.baidu.com';
        // 设置请求参数并发起登录请求
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
            throw new \RuntimeException('Login Baidu developer background for authorization error');
        }

        return $res;
    }

    public function setRequest($data)
    {
        $this->cookieJar = storage_path('cookie');

        $this->curlopt = [
            CURLOPT_AUTOREFERER => 1, // 启用自动跳转
            CURLOPT_FOLLOWLOCATION => 1, // 启用跟随跳转
            CURLOPT_RETURNTRANSFER => 1, // 启用将请求的 url 的信息原生返回，不要渲染
            CURLOPT_CONNECTTIMEOUT => 10, // 设置请求超时时间 单位 s
            CURLOPT_COOKIEJAR => $this->cookieJar, // 设置将该次请求所得到的所有 cookie 写入到定义的文件中去
            CURLOPT_COOKIEFILE => $this->cookieJar, // 设置该次请求所携带的 cookie 文件
            CURLOPT_POST => 1, // 设置 POST 请求
            CURLOPT_POSTFIELDS => $data, // 设置 POST 请求的内容
            CURLOPT_HTTPHEADER => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36', // 设置 User-Agent
                'Refere' => $this->refere // 定义来源地址
            ],
        ];
        return $this;
    }

    public function setAuth($source)
    {
        return $this->source($source)->secret_key(secret_key($source));
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
        if (func_num_args() == 1 && !empty(func_get_arg(0))) {
            $this->api_url = func_get_arg(0);
        } else {
            $this->api_url = bd_api_url();
        }
        return $this->api_url;
    }

    public function source($source)
    {
        $this->source = $source;
        return $this;
    }

    public function secret_key($secret_key)
    {
        $this->secret = $secret_key;
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
    public function buildRes($cmd, $data = array(), $encode = 1, $errno = 0, $error = 'success')
    {
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
     * @param string $cache_key
     * @return mixed
     */
    public function shopInfoFromCache($data = null, $expire = 1440, $cache_key = 'shop:yilianyun:')
    {
        $cache_key .= $this->shop_id;
        $data = \Cache::remember($cache_key, $expire, function () use ($data) {
            if (is_array($data)) {
                return $data;
            }
            $res = $this->send(['baidu_shop_id' => $data], bdwm_info_url());
            if (isset($res['status']) && $res['status'] == 0) {
                return $res['data'];
            }
        });

        if (is_null($data)) {
            \Cache::forget($cache_key);
        }

        return $data;
    }
}
