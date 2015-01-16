<?php
namespace Subbly\Frontend\Helpers;

use \Handlebars\Context;
use \Handlebars\Helper;
use \Handlebars\Template;

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
      $routesMap = \Config::get('subbly.frontendUri');

      foreach( $routesMap as $uri => $page )
      {
        if( $page == $args[0] )
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
    // find matches against the regex and replaces them the callback function.
    return $root . preg_replace_callback(

       // Matches parts to be replaced: '{var}'
       '/(\{.*?\})/',

       // Callback function. Use 'use()' or define arrays as 'global'
       function( $matches ) use ( $context, $schema )
       {
          $var = trim( $matches[1], '{}' );

           // Remove curly brackets from the match
           // then use it as variable name
          $str = ( isset( $context[ $var ] ) ) 
                 ? $context[ $var ]
                 : $var;

          // Pick an item from the related array whichever.
          return $str;
       },

       // Input string to search in.
       $schema
    );    
  }
}
