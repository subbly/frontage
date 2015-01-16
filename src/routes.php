<?php

/*
 * Register template driven Frontend
 * Comment this part if you want to user
 * your own controller
 */

Route::any('{url}', 'Subbly\Frontend\AutoController@run')->where('url', '.*');
