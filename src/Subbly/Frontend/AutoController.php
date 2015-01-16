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
      $engine->addHelper( 'products', new Helpers\ProductsHelper() );
      $engine->addHelper( 'product',  new Helpers\ProductHelper() );
      $engine->addHelper( 'images',   new Helpers\ProductImagesHelper() );
      $engine->addHelper( 'image',    new Helpers\ProductDefaultImageHelper() );
      $engine->addHelper( 'url',      new Helpers\UrlHelper() );
      $engine->addHelper( 'assets',   new Helpers\AssetsHelper() );
      $engine->addHelper( 'price',    new Helpers\PriceHelper() );
      $engine->addHelper( 'compare',  new Helpers\CompareHelper() );

      # Will render the model to the templates/main.tpl template
      // TODO: add cache
      return $engine->render( $_route, [
          'inputs'   => $this->params
        , 'themes'   => $themePublic
        , 'settings' => $settings
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
