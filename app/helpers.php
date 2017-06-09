<?php

function apply_method($bd_cmd)
{
    return app('classfactory')->applyMethod($bd_cmd);
}

function array_explode($string, $delimiter = '.')
{
    if (!isset($string)) {
        throw new InvalidArgumentException('缺少必要参数');
    }

    if (is_string($string)) {
        return explode($delimiter, $string);
    }
}

function bd_api_url()
{
    return env('BD_API_URL', 'http://api.waimai.baidu.com/');
}

function bd_secret()
{
    return env('BD_SECRET', 'default');
}

function bd_source()
{
    return env('BD_SOURCE', '00000');
}

/**
 * 生成签名
 *
 * @param array $data
 * @return string
 */
function gen_sign(array $data)
{
    $arr = array();
    $arr['body']      = $data['body'];
    $arr['cmd']       = $data['cmd'];
    $arr['encrypt']   = $data['encrypt'];
    $arr['secret']    = $data['secret'];
    $arr['source']    = $data['source'];
    $arr['ticket']    = $data['ticket'];
    $arr['timestamp'] = $data['timestamp'];
    $arr['version']   = $data['version'];
    ksort($arr);

    $tmp = array();
    foreach ($arr as $key => $value) {
        $tmp[] = "$key=$value";
    }

    $strSign = implode('&', $tmp);
    $sign = strtoupper(md5($strSign));

    return $sign;
}

function uuid()
{
    return (\Ramsey\Uuid\Uuid::uuid1())->getHex();
}