<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class UrlHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{url 'filename'}}
   * {{url 'filename' this }}
   * {{url 'filename' with {"id":12, "slug":"test"} }}
   *
   * @param \Handlebars\Template $template The template instance
   * @param \Handlebars\Context  $context  The current context
   * @param array                $args     The arguments passed the the helper
   * @param string               $source   The source
   *
   * @return mixed
   */
  public function execute( Template $template, Context $context, $args, $source )
  {
    $buffer       = \URL::to('/');
    $props        = $this->parseProps( $args, $context );
    $args         = $template->parseArguments( $args );
    $routePattern = false;
    $routeVars    = false;
    $uri          = false;

    $countAgrs = count( $args );

    if( !$countAgrs )
    {
      return $buffer;      
    }
    else
    {
      $routesMap = \Config::get('subbly.frontageUri');

      foreach( $routesMap as $uri => $page )
      {
        // prevent restricted page
        $page = explode('@', $page);

        if( $page[0] == $args[0] )
        {
          $routePattern = $uri;
          preg_match_all( '/\{(.*?)\}/', $uri, $matches );

          $routeVars = array_map(function($m) { return trim( $m, '?' ); }, $matches[1]);
          break;
        }
      }

      if( !$routePattern )
        return $buffer;
    }

    if( !$props && $countAgrs === 2 && $args[1] == 'this' )
    {
      return $this->buildUri( $context->get('this'), $routePattern, $buffer );
    }
    else if( $props && $countAgrs === 1 )
    {
      return $this->buildUri( $props, $routePattern, $buffer );
    }
    else
    {
      return $routePattern;
    }
  }

  private function buildUri( $context, $schema, $root )
  {
      preg_match_all( '/\{(.*?)\}/', $schema, $matches );

      $optionals  = array_map(function($m) { return trim( $m, '?' ); }, $matches[1]);

      $clean      = preg_replace('/\{(\w+?)\?\}/', '@$1', $schema );
      $clean2     = preg_replace('/\{(\w+?)\}/', '@$1', $clean );

      $exploded   = preg_split('/[\/:-]/', $clean2 );
      $separators = preg_split('/[^\/:-]/', $clean, -1, PREG_SPLIT_NO_EMPTY );

      $params     = [];

      foreach( $exploded as $key => $value )
      {
        $pos = strpos( $value, '@');

        // prefix route's slash
        if( empty( $value ) )
          continue;

        if( $pos === false )
        {
          $params[] = $value;
          continue;
        }

        $var = ltrim( $value, '@' );

        if( !isset( $context[ $var ] ) )
          continue;

        $params[] = $context[ $var ];
      }

      $link = '';

      foreach( $params as $key => $param )
      {
        $link .= $separators[ $key ] . $param;
      }

      return $link;
  }
}
