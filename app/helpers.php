<?php

function apply_method($bd_cmd)
{
    return app('classfactory')->applyMethod($bd_cmd);
}

function array_explode($string, $delimiter = '.')
{
    if (!isset($string)) {
        throw new InvalidArgumentException('array.explode paramter missing' . requiest()->ip());
    }

    if (is_string($string)) {
        return explode($delimiter, $string);
    }
}

function y_api_url()
{
    return config('baidutakeout.yilianyun_print_api_url');
}

function bd_api_url()
{
    return config('baidutakeout.baidu_take_out_api_url');
}

function bdwm_info_url()
{
    return config('baidutakeout.baidu_shop_info_url');
}

function source_info_url()
{
    return config('baidutakeout.baidu_source_info_url');
}

function no_upper_limit_source_info_url()
{
    return config('baidutakeout.baidu_no_upper_limit_source_info_url');
}

/**
 * 生成百度签名
 *
 * @param array $data
 * @return string
 */
function gen_baidu_sign(array $data)
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

/**
 * 生成易联云签名
 *
 * @param string $content
 * @return array
 */
function gen_y_sign_and_data($content, array $shopInfo, $key = 0)
{
    $machine = $shopInfo['machines'][$key];

    $params['partner'] = $shopInfo['user_id'];
    $params['machine_code'] = $machine['mkey'];
    $params['time'] = time();

    ksort($params);
    $stringToBeSigned = $shopInfo['api_key'];
    foreach ($params as $k => $v) {
        $stringToBeSigned .= urldecode($k.$v);
    }
    $stringToBeSigned .= $machine['msign'];

    $params['sign'] = strtoupper(md5($stringToBeSigned));
    $params['content'] = $content;
    return $params;
}

function getNumber($data)
{
    return number_format($data / 100, 2);
}

function offset(&$str, array $offset, $delimiter = '-')
{
    foreach ($offset as $i => $v) {
        $str = mb_substr_replace($str, $delimiter, $i + $v, 0);
    }
    return $str;
}

 function font_size($key)
 {
     $font['confirm_time'] = 1; // 下单时间字体大小
    $font['order_id'] = 1; // 订单号字体大小
    $font['address'] = 2; // 收货人地址字体大小
    $font['info'] = 2; // 收货信息字体大小
    $font['remark'] = 2; // 备注字体大小
    $font['table'] = 2; // 商品字体大小
    $font['mn'] = 1; // 多联设置
    $font['ad1'] = null; // 自定义广告语

    $font['default'] = 2;

     return $font[$key];
 }

function uuid($offset = [8,12,16,20])
{
    $uuid1 = \Ramsey\Uuid\Uuid::uuid1();
    $str = $uuid1->getHex();
    offset($str, $offset);
    return strtoupper($str);
}

function mb_substr_replace($string, $replacement, $start, $length = null)
{
    if (is_array($string)) {
        $num = count($string);
        // $replacement
        $replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
        // $start
        if (is_array($start)) {
            $start = array_slice($start, 0, $num);
            foreach ($start as $key => $value) {
                $start[$key] = is_int($value) ? $value : 0;
            }
        } else {
            $start = array_pad(array($start), $num, $start);
        }
        // $length
        if (!isset($length)) {
            $length = array_fill(0, $num, 0);
        } elseif (is_array($length)) {
            $length = array_slice($length, 0, $num);
            foreach ($length as $key => $value) {
                $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
            }
        } else {
            $length = array_pad(array($length), $num, $length);
        }
        // Recursive call
        return array_map(__FUNCTION__, $string, $replacement, $start, $length);
    }
    preg_match_all('/./us', (string)$string, $smatches);
    preg_match_all('/./us', (string)$replacement, $rmatches);
    if ($length === null) {
        $length = mb_strlen($string);
    }
    array_splice($smatches[0], $start, $length, $rmatches[0]);
    return join($smatches[0]);
}

function source($source, $source_info_key = 'bdwm:shop:')
{
    $info_key = $source_info_key . $source;
    $sourceInfo = sourceInfoFromCache($source, $info_key);
    if (is_null($sourceInfo)) {
        \Cache::forget($info_key);
        return $sourceInfo;
    }
    return $sourceInfo['source'];
}

function secret_key($source, $source_info_key = 'bdwm:shop:')
{
    $info_key = $source_info_key . $source;
    $sourceInfo = sourceInfoFromCache($source, $info_key);
    if (is_null($sourceInfo)) {
        \Cache::forget($info_key);
        return $sourceInfo;
    }
    return $sourceInfo['secret'];
}

function sourceInfoFromCache($source, $info_key)
{
    return \Cache::rememberForever($info_key, function () use ($source) {
        $res = app('baidu')->send(compact('source'), source_info_url());
        if ($res['status'] == 0) {
            return $res['data'];
        }
    });
}

function sourceInfoExists($info_key)
{
    return \Cache::has($info_key);
}

function bug_email()
{
    return config('baidutakeout.bug.emails');
}

function first_no_null(&$var, $default = '')
{
    return !empty($var) ? $var : $default;
}
