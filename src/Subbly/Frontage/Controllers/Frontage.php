<?php

namespace Subbly\Frontage\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;
use Subbly\Frontage\Helpers as Helpers;
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
  );

  protected function run()
  {
    $routesMap    = Config::get('subbly.frontageUri'); 
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

      // $engine->addHelper( 'compare',       new Helpers\Usefull\CompareHelper() );
      // $engine->addHelper( 'upper',         new Helpers\Usefull\UpperHelper() );
      // $engine->addHelper( 'lower',         new Helpers\Usefull\LowerHelper() );
      // $engine->addHelper( 'capitalize',    new Helpers\Usefull\CapitalizeHelper() );
      // $engine->addHelper( 'capitalizeAll', new Helpers\Usefull\CapitalizeAllHelper() );
      // $engine->addHelper( 'formatDate',    new Helpers\Usefull\FormatDateHelper() );
      // $engine->addHelper( 'truncate',      new Helpers\Usefull\TruncateHelper() );
      // $engine->addHelper( 'default',       new Helpers\Usefull\DefaultHelper() );


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
      App::abort( 404, $e->getMessage() );
    }
    catch( Exception $e )
    {
      App::abort( 500, 'An error occurred' );
    }
  }
}
