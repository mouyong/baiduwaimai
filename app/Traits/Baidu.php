<?php

namespace App\Traits;

use Zttp\Zttp;

trait Baidu
{
    protected $secret;
    protected $source;
    protected $encrypt;
    protected $version;
    protected $timestamp;
    protected $zttp;

    protected function set()
    {
        $this->secret  = bd_secret();
        $this->source  = bd_source();
        $this->encrypt = '';
        $this->version = 3;

        $this->zttp = Zttp::asFormParams();
    }

    /**
     * 生成请求
     *
     * @param string $cmd
     * @param string $ticket
     * @param array $body
     * @param int $encode  json_encode 转码
     * @return mixed
     */
    protected function buildCmd(string $cmd, string $ticket, array $body, $encode = 1)
    {
        $req['cmd']       = $cmd;
        $req['source']    = $this->source;
        $req['secret']    = $this->secret;
        $req['ticket']    = $ticket;
        $req['version']   = $this->version;
        $req['encrypt']   = $this->encrypt;
        $req['timestamp'] = isset($this->timestamp) ? $this->timestamp : time();
        $req['body']      = $body;
        $req['body']      = json_encode($req['body']);
        ksort($req);

        $req['sign']      = gen_sign($req);

        $req['body']      = $encode ? $req['body'] : json_decode($req['body'], 1);
        return $req;
    }

    /**
     * 生成响应
     *
     * @param string $cmd
     * @param string $ticket
     * @param array $data
     * @param int $encode  json_encode 转码
     * @param int $errno
     * @param string $error
     * @return mixed
     */
    public function buildRes(
        string $cmd,
        string $ticket,
        $data = array(),
        $encode = 1,
        $errno = 0,
        $error = 'success'
    ) {
        $body['errno'] = $errno;
        $body['error'] = $error;
        $body['data']  = $data;

        return $this->buildCmd($cmd, $ticket, $body, $encode);
    }
}
