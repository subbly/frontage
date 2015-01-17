<?php

namespace Subbly\Frontend;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\Routing;
use \Handlebars\Handlebars;
use \Handlebars\Loader\FilesystemLoader;
use \App;
use \Config;
use \View;

class AutoController 
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
  );

  protected function run()
  {
    $routesMap    = Config::get('subbly.frontendUri'); 
    $currentTheme = Config::get('subbly.theme');

    $themePath    = TPL_PUBLIC_PATH . DS . $currentTheme . DS;
    $themePublic  = \URL::to( '/themes/' . $currentTheme . DS );

    $settings     = \Subbly\Subbly::api('subbly.setting')->all()->toArray();

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

      extract( $routeParams, EXTR_SKIP );

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
          ]
      );

      // init Handlebars
      $engine = new Handlebars([
          'loader'          => $partialsLoader
        , 'partials_loader' => $partialsLoader
      ]);

      // add Subbly's helpers
      $engine->addHelper( 'products',      new Helpers\ProductsHelper() );
      $engine->addHelper( 'product',       new Helpers\ProductHelper() );
      $engine->addHelper( 'images',        new Helpers\ProductImagesHelper() );
      $engine->addHelper( 'image',         new Helpers\ProductDefaultImageHelper() );
      $engine->addHelper( 'url',           new Helpers\UrlHelper() );
      $engine->addHelper( 'assets',        new Helpers\AssetsHelper() );
      $engine->addHelper( 'price',         new Helpers\PriceHelper() );
      $engine->addHelper( 'compare',       new Helpers\CompareHelper() );
      $engine->addHelper( 'upper',         new Helpers\UpperHelper() );
      $engine->addHelper( 'lower',         new Helpers\LowerHelper() );
      $engine->addHelper( 'capitalize',    new Helpers\CapitalizeHelper() );
      $engine->addHelper( 'capitalizeAll', new Helpers\CapitalizeAllHelper() );
      $engine->addHelper( 'formatDate',    new Helpers\FormatDateHelper() );
      $engine->addHelper( 'truncate',      new Helpers\TruncateHelper() );
      $engine->addHelper( 'default',       new Helpers\DefaultHelper() );

      # Will render the model to the templates/main.tpl template
      // TODO: add cache
      return $engine->render( $_route, [
          'inputs'   => $this->params
        , 'themes'   => $themePublic
        , 'settings' => $settings
        // tests
        , 'name'     => 'Test PAGE'
        , 'isActive' => false
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
      App::abort( 404, 'Page Not Found' );
    }
    catch( Exception $e )
    {
      App::abort( 500, 'An error occurred' );
    }
  }
}
