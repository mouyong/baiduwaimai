<?php

/*
|--------------------------------------------------------------------------
| Models Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Models factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\User::class, 'admin', function (Faker\Generator $faker) {
    return array(
       'id' => '3',
       'user_id' => '626',
       'baidu_shop_id' => '1717041709',
       'order_auto_confirm' => 'yes',
       'api_key' => '8c61ff8e4d1b6ed9930f6cb21029f67df630f92a',
       'machines' =>
           array(
               0 =>
                   array(
                       'id' => '79987',
                       'mkey' => '1400450905',
                       'msign' => 'enck2sfnujen',
                       'version' => 0,
                   ),
           ),
       'fonts_setting' =>
           array(
               'receive_info_size' => '2',
               'receive_address_size' => '1',
               'order_size' => '1',
               'create_order_size' => '1',
               'remark_size' => '2',
               'product_size' => '2',
               'mn' => '2',
               'ad' => '3',
               'shop_ad_content' => '《地方撒》alert&lt;?php echo $a;?&gt;
&lt;p&gt;this -&amp;gt; &amp;quot;&lt;/p&gt;',
               'default' => '2',
           ),
   );
});
