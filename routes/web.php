<?php

Route::get('/', function() {
    $response = array (
      'cmd' => 'order.create',
      'timestamp' => '1496905644',
      'version' => '3',
      'ticket' => 'DF13FDF6-C51D-F4E8-B9F0-982A03885177',
      'source' => '64824',
      'body' => '{"order_id":"14969056410962"}',
      'sign' => '231A86DEA715C94FFF7329F2B17A581F',
      'encrypt' => NULL,
    );

    unset($response['sign']);
    $response['secret'] = bd_sk();
    ksort($response);

    $string = sign_string($response);

    dd($string, sign_encode($string));

    return $response;
});
