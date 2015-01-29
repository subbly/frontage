<?php

namespace Subbly\Frontage\Helpers;

abstract class CustomHelper 
  implements \Handlebars\Helper
{
  protected function getRouteUri( $routeName )
  {
    $routesMap = \Config::get('subbly.frontageUri');

    foreach( $routesMap as $uri => $page )
    {
      if( $page == $routeName )
      {
        return $uri;
      }
    }
  }

  public function parseProps( &$args, $context )
  {
    $args = preg_replace( "/[\n\r]/", '', $args );

    $pattern = '/with (\{(.*?)\})/';

    preg_match( $pattern, $args, $properties );

    $properties = ( isset( $properties[ 1 ] ) ) ? json_decode( $properties[ 1 ], true ) : false;

    if( is_array( $properties ) && count( $properties ) )
    {
      foreach( $properties as $key => $value )
      {
        if( is_string( $value ) )
        {
          preg_match( '/^@@(.*)/', $value, $property );

          if( isset( $property[1] ) )
          {
            $properties[ $key ] = $context->get( $property[1] );
            break;            
          }

          preg_match( '/^@(.*)/', $value, $property );

          if( isset( $property[1] ) )
          {
            $properties[ $key ] = $context->get( $property[1] );
          }
        }
      }

      $args = preg_replace( $pattern, '', $args );
      
      return $properties;
    }

    return false;
  }

  public function parseArgs($args)
  {
    //regular expression generated from ((?<=")(?:\\.|[^"\\])*(?="))|(\w+)  with http://regex.larsolavtorvik.com/
    preg_match_all('/(?<=")[^"]*(?=")|(\w+)/i', $args, $result);
    return $result[0];
  }
}
