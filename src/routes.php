<?php

/*
 * Register template driven Frontend
 * Comment this part if you want to user
 * your own controller
 */

Route::any('{url}', 'Subbly\Frontend\Controllers\AutoController@run')->where('url', '.*');
