<?php

namespace Subbly\Frontend;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\Routing;
use \Illuminate\Routing\Controller;
use \App;
use \Config;
use \View;

class AutoController 
  extends BaseController 
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

  /**
   * The layout that should be used for responses.
   */
  protected $layout = 'layouts.master';

  protected function run()
  {
    $routesMap    = Config::get('subbly.frontendUri'); 
    $currentTheme = Config::get('subbly.theme');

    $themePath    = TPL_PUBLIC_PATH . DS . $currentTheme . DS;

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

    $options =  array( 'extension' => '.php' );

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
      
      return View::make('user.profile')
              ->with( 'input', $this->params )
              ->with( 'var', 1420485310 );
    }
    catch( Routing\Exception\ResourceNotFoundException $e )
    {
      App::abort( 404, 'Page Not Found' );
    }
    catch( Exception $e )
    {
      App::abort( 500, 'An error occurred' );
    }
  }
}
