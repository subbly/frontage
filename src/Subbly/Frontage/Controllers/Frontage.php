<?php

namespace Subbly\Frontage\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;
use Subbly\Frontage\Helpers as Helpers;
use Subbly\Subbly as Subbly;
use App;
use Config;
use View;

class Frontage 
  extends \Controller
{
  /**
   * Defaults values
   */
  protected $params = array(
      'id'          => false
    , 'slug'        => false
    , 'page'        => 1
    , 'category'    => false
    , 'subcategory' => false
    , 'currentpage' => false
    , 'addressId'   => false
    , 'orderId'     => false
    , 'productId'   => false
  );

  protected function run()
  {
    $routesMap    = Config::get('subbly.frontageUri'); 
    $currentTheme = Config::get('subbly.theme');

    $themePath    = TPL_PUBLIC_PATH . DS . $currentTheme . DS;
    $themePublic  = \URL::to( '/themes/' . $currentTheme . DS );

    // Queries
    $settings     = Subbly::api('subbly.setting')->all()->toArray();
    $isUserLogin  = Subbly::api('subbly.user')->check();


    $request = Request::createFromGlobals();
    $routes  = new Routing\RouteCollection();

    foreach( $routesMap as $uri => $page )
    {
      preg_match_all( '/\{(.*?)\}/', $uri, $matches );

      $optionals = array_map(function($m) { return trim( $m, '?' ); }, $matches[1]);

      $uri = preg_replace('/\{(\w+?)\?\}/', '{$1}', $uri);

      $routes->add( $page , new Routing\Route( $uri, $optionals ) );
    }

    unset( $page );

    $context = new Routing\RequestContext();
    $context->fromRequest( $request );

    $matcher = new Routing\Matcher\UrlMatcher( $routes, $context );

    // Tremplates

    try 
    {
      $routeParams = $matcher->match( $request->getPathInfo() );

      // Extract the $_route variable
      extract( $routeParams, EXTR_SKIP );

      // match loggued user restriction
      preg_match( '/([[:ascii:]]+)(@([[:ascii:]]+)?)/i', $_route, $matches );

      // Current User
      $currentUser = ( $isUserLogin )
                     ? Subbly::api('subbly.user')->currentUser()
                     : false;

      // need auth
      if( count( $matches ) > 0 )
      {
        // user is not logged
        if( !$currentUser )
        {
          // if there a redirect route declared
          if( isset( $matches[3] ) && !empty( $matches[3] ) )
          {
            $url = array_search( $matches[3], $routesMap );

            // if redirect route exist
            if( $url )
            {
              // TODO: add error flash message
              return \Redirect::to( $url );            
            }
          }
          
          throw new \InvalidArgumentException('No matching url', 1);
        }

        // route's filename 
        $_route = $matches[1];
      }

      foreach( $routeParams as $key => $value )
      {
        if( isset( $this->params[ $key ] ) )
        {
          $this->params[ $key ] = $value;
        }
      }

      $this->params['currentpage'] = $_route;

      $partialsDir = $themePath;

      // Filesystem's options
      $partialsLoader = new FilesystemLoader( $partialsDir, [
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
      return $engine->render( $_route, [
          'inputs'      => $this->params
        , 'themes'      => $themePublic
        , 'settings'    => $settings
        , 'isUserLogin' => $isUserLogin
        , 'user'        => $currentUser
        // tests
        , 'name'     => 'Test PAGE'
        , 'isActive' => false
        , 'first'    => true
        , 'second'   => 'a'
        , 'other_genres' => 
          [
              'genres' => 
              [
                  'yop'
                , 'test'
              ]
          ]
        , 'genres' => 
          [
                'Hip-Hop'
              , 'Rap'
              , 'Techno'
              , 'Country'
          ]
        , 'object' => [
            'key' => 'value'
          ]
        , 'cars' => 
          [
            [
              'category' => 'Foreign',
              'count' => 4,
              'list' => [
                  'Toyota',
                  'Kia',
                  'Honda',
                  'Mazda'
              ]
            ]
          , [
              'category' => 'WTF',
              'count' => 1,
              'list' => [
                  'Fiat'
              ]
            ]
          , [
              'category' => 'Luxury',
              'count' => 2,
              'list' => [
                  'Mercedes Benz',
                  'BMW'
              ]
            ]
          , [
              'category' => 'Rich People Shit',
              'count' => 3,
              'list' => [
                  'Ferrari',
                  'Bugatti',
                  'Rolls Royce'
              ]
            ]
        ]
      ]);
    }
    catch( \InvalidArgumentException $e )
    {
      App::abort( 404, $e->getMessage() );
    }
    catch( \Exception $e )
    {
      App::abort( 500, $e->getMessage() );
    }
  }
}
