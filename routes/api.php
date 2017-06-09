<?php

use Illuminate\Support\Facades\Input;

Route::post('/order', function () {
    return apply_method(Input::get('cmd'));
});
