<?php

Route::get('/hadoop/dfshealth.jsp', function () {
    return "I'm health";
});

Route::get('/', function() {
    return ['errno' => 403, 'error' => 'unauthorized action.'];
});
