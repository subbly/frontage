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
    $routesMap    = Config::get('subbly.FrontageUri'); 
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
      $engine->addHelper( 'products',      new Helpers\Subbly\ProductsHelper() );
      $engine->addHelper( 'product',       new Helpers\Subbly\ProductHelper() );
      $engine->addHelper( 'images',        new Helpers\Subbly\ProductImagesHelper() );
      $engine->addHelper( 'image',         new Helpers\Subbly\ProductDefaultImageHelper() );
      $engine->addHelper( 'url',           new Helpers\Subbly\UrlHelper() );
      $engine->addHelper( 'assets',        new Helpers\Subbly\AssetsHelper() );
      $engine->addHelper( 'price',         new Helpers\Subbly\PriceHelper() );
      $engine->addHelper( 'formErrors',    new Helpers\Subbly\FormErrorsHelper() );
      $engine->addHelper( 'isUserLogin',   new Helpers\Subbly\UserCheckHelper() );
      $engine->addHelper( 'compare',       new Helpers\Usefull\CompareHelper() );
      $engine->addHelper( 'upper',         new Helpers\Usefull\UpperHelper() );
      $engine->addHelper( 'lower',         new Helpers\Usefull\LowerHelper() );
      $engine->addHelper( 'capitalize',    new Helpers\Usefull\CapitalizeHelper() );
      $engine->addHelper( 'capitalizeAll', new Helpers\Usefull\CapitalizeAllHelper() );
      $engine->addHelper( 'formatDate',    new Helpers\Usefull\FormatDateHelper() );
      $engine->addHelper( 'truncate',      new Helpers\Usefull\TruncateHelper() );
      $engine->addHelper( 'default',       new Helpers\Usefull\DefaultHelper() );
      $engine->addHelper( 'loginFrom',     new Helpers\Post\LoginHelper() );

      // Layout helpers
      $storage = new Helpers\Layout\BlockStorage();

      $engine->addHelper('block',            new Helpers\Layout\BlockHelper($storage));
      $engine->addHelper('extends',          new Helpers\Layout\ExtendsHelper($storage));
      $engine->addHelper('override',         new Helpers\Layout\OverrideHelper($storage));
      $engine->addHelper('ifOverridden',     new Helpers\Layout\IfOverriddenHelper($storage));
      $engine->addHelper('unlessOverridden', new Helpers\Layout\UnlessOverriddenHelper($storage));


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
