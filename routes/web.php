<?php

Route::get('/hadoop/dfshealth.jsp', function () {
    return "I'm health";
});

Route::get('/', function (GuzzleHttp\Client $client) {
    return ['errno' => 403, 'error' => 'unauthorized action.'];
    
    // $tables = DB::select('show tables');
    
    // $tables = collect($tables)->map(function ($i) {
    //     return $i->Tables_in_bdwm;
    // })->toArray();
    
    // $config = [
    //     'driver'     => 'mysql',
    //     'user'       => 'root',
    //     'password'   => 'root',
    //     'connection' => [
    //         'host'    => 'mysql',
    //         'port'    => '3306',
    //         'dbname'  => 'bdwm',
    //         'charset' => 'UTF8',
    //     ],
    //     'option'     => [
    //         PDO::ATTR_PERSISTENT=>true,
    //     ],
    // ];

    // foreach ($config['connection'] as $k => $v) {
    //     $data[] = "$k=$v";
    // }
    // $dsn = $config['driver'] . ':' . implode(';', $data);

    // $db = new PDO($dsn, $config['user'], $config['password'], $config['option']);
    // $db->query("SET NAMES utf8;");

    // $st = $db->prepare('SHOW TABLES');
    // $bool = $st->execute();
    
    // if (! $bool) {
    //     throw new \Exception("No data");
    // }

    // $data = $st->fetchAll(PDO::FETCH_COLUMN);
    // dd($data);
    $url = 'http://www.yueloo.com/index.php?controller=home&method=getRealTimeReward';

    $client = new GuzzleHttp\Client();
    $res = $client->get($url);

    $data = json_decode($res->getBody(), 1);
    
    dd($data);
});
