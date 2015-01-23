<?php

/*
 * Register Debugbar assets's path
 */
if( Config::get('app.debug', false) )
{
  Route::get('_debugbar/assets/stylesheets', array(
      'uses' => 'Barryvdh\Debugbar\Controllers\AssetController@css'
    , 'as'   => 'debugbar.assets.css'
  ));

   Route::get('_debugbar/assets/javascript', array(
      'uses' => 'Barryvdh\Debugbar\Controllers\AssetController@js'
    , 'as'   => 'debugbar.assets.js'
  ));
}

/*
 * Register template driven Frontage
 * Comment this part if you want to user
 * your own controller
 */

Route::post('/login', array(
    'as'   => 'frontage.form.login'
  , 'uses' => 'Subbly\Frontage\Controllers\Login@run'
));

Route::any('{url}', 'Subbly\Frontage\Controllers\Frontage@run')->where('url', '.*');
