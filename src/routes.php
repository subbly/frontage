<?php

/*
 * Register template driven Frontend
 * Comment this part if you want to user
 * your own controller
 */

Route::post('/login', array(
    'as'   => 'frontage.form.login'
  , 'uses' => 'Subbly\Frontend\Controllers\Login@run'
));

Route::any('{url}', 'Subbly\Frontend\Controllers\Frontage@run')->where('url', '.*');
