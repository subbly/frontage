<?php

use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;
use Subbly\Frontage\Helpers as Helpers;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Subbly\Subbly as Subbly;

/*
 * Register Debugbar assets's path
 */
Route::get('_debugbar/assets/stylesheets', [
    'uses' => 'Barryvdh\Debugbar\Controllers\AssetController@css'
  , 'as'   => 'debugbar.assets.css'
]);

 Route::get('_debugbar/assets/javascript', [
    'uses' => 'Barryvdh\Debugbar\Controllers\AssetController@js'
  , 'as'   => 'debugbar.assets.js'
]);


/*
 * Register Frontend reserved post routes
 */

Route::group( [
      'prefix' => Config::get( 'subbly.frontagePostUri', '/subbly' )
    , 'before' => 'csrf'
], function() 
{
  Route::post('/login', [
      'as'     => 'frontage.form.login'
    , 'uses'   => 'Subbly\Frontage\Controllers\Login@run'
  ]);


  Route::post('/account/address/{id?}', [
      'as'     => 'frontage.form.address'
    , 'uses'   => 'Subbly\Frontage\Controllers\Address@run'
  ]);
});

/*
 * Register template driven Frontage
 * Comment this part if you want to user
 * your own controller
 */
foreach( Config::get('subbly.frontageUri') as $uri => $tpl )
{
  // echo $uri.'<br>';
  Route::get( $uri, function() use ( $uri, $tpl )
  {
    $currentTheme = Config::get('subbly.theme');

    $themePath    = TPL_PUBLIC_PATH . DS . $currentTheme . DS;
    $themePublic  = \URL::to( '/themes/' . $currentTheme . DS );

    preg_match_all( '/\{(.*?)\}/', $uri, $routeArgs );
    $inputs = array_map(function($m) { return trim( $m, '?' ); }, $routeArgs[1]);
// dd( $inputs );
    // match loggued user restriction
    preg_match( '/([[:ascii:]]+)(@([[:ascii:]]+)?)/i', $tpl, $restricted );

    // dd(func_get_args(), $inputs, $uri, $tpl, $restricted );

    // Queries
    $settings     = Subbly::api('subbly.setting')->all()->toArray();
    $isUserLogin  = Subbly::api('subbly.user')->check();

    // Current User
    $currentUser = ( $isUserLogin )
                   ? Subbly::api('subbly.user')->currentUser()
                   : false;

    // need auth
    if( count( $restricted ) > 0 )
    {
      // user is not logged
      if( !$currentUser )
      {
        // if there a redirect route declared
        if( isset( $restricted[3] ) && !empty( $restricted[3] ) )
        {
          $url = array_search( $restricted[3], Config::get('subbly.frontageUri') );

          // if redirect route exist
          if( $url )
          {
            // TODO: add error flash message
            return \Redirect::to( $url );            
          }
        }

        App::abort(401, 'Authentication needed');
      }

      // route's filename 
      $tpl = $restricted[1];
    }

    /**
     * Defaults values
     */
    $params = [
        'page'        => 1
      , 'category'    => false
      , 'subcategory' => false
      , 'currentpage' => false
      , 'addressId'   => false
      , 'orderId'     => false
      , 'productId'   => false
      , 'productSku'  => false
      , 'productSlug' => false
    ];


    // Override defaults URI variables
    foreach( func_get_args() as $key => $value )
    {
      $params[ $inputs[ $key ] ] = $value;
    }

    // dd(func_get_args(), $inputs, $params, $uri, $tpl, $restricted );

    // TPL Engine

    // Filesystem's options
    $partialsLoader = new FilesystemLoader( $themePath, [
        'extension' => 'html'
    ]);

    // init Handlebars
    $engine = new Handlebars([
        'loader'          => $partialsLoader
      , 'partials_loader' => $partialsLoader
    ]);

    $helpers = new \JustBlackBird\HandlebarsHelpers\Helpers();
    
    // init Handlebars
    $engine = new Handlebars([
        'loader'          => $partialsLoader
      , 'partials_loader' => $partialsLoader
      , 'helpers'         => $helpers
    ]);

    $nativeHelpers = Config::get('frontage::helpers');

    // Load native Helpers
    foreach( $nativeHelpers as $key => $class )
    {
      $engine->addHelper( $key, new $class() );
    }

    # Will render the model to the templates/main.tpl template
    // TODO: add cache
    return $engine->render( $tpl, [
        'inputs'      => $params
      , 'themes'      => $themePublic
      , 'settings'    => $settings
      , 'isUserLogin' => $isUserLogin
      , 'user'        => $currentUser
      // tests
      // , 'name'     => 'Test PAGE'
      // , 'isActive' => false
      // , 'first'    => true
      // , 'second'   => 'a'
      // , 'other_genres' => 
      //   [
      //       'genres' => 
      //       [
      //           'yop'
      //         , 'test'
      //       ]
      //   ]
      // , 'genres' => 
      //   [
      //         'Hip-Hop'
      //       , 'Rap'
      //       , 'Techno'
      //       , 'Country'
      //   ]
      // , 'object' => [
      //     'key' => 'value'
      //   ]
      // , 'cars' => 
      //   [
      //     [
      //       'category' => 'Foreign',
      //       'count' => 4,
      //       'list' => [
      //           'Toyota',
      //           'Kia',
      //           'Honda',
      //           'Mazda'
      //       ]
      //     ]
      //   , [
      //       'category' => 'WTF',
      //       'count' => 1,
      //       'list' => [
      //           'Fiat'
      //       ]
      //     ]
      //   , [
      //       'category' => 'Luxury',
      //       'count' => 2,
      //       'list' => [
      //           'Mercedes Benz',
      //           'BMW'
      //       ]
      //     ]
      //   , [
      //       'category' => 'Rich People Shit',
      //       'count' => 3,
      //       'list' => [
      //           'Ferrari',
      //           'Bugatti',
      //           'Rolls Royce'
      //       ]
      //     ]
      // ]
    ]);

  });
}

/*
 * Exception handling
 */

App::missing( function( $exception )
{
  exit('404');
    // return Response::view('errors.missing', array(), 404);
});

App::error( function( MethodNotAllowedHttpException $exception )
{
    exit('503');
});

// Route::any('{url}', 'Subbly\Frontage\Controllers\Frontage@run')->where('url', '.*');
