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
      // match loggued user restriction
      preg_match( '/([[:ascii:]]+)(@([[:ascii:]]+)?)/i', $page, $matches );

      // need auth
      if( count( $matches ) > 0 )
      {
        // route's filename 
        $page = $matches[1];
      }

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

  public function parseArgs( $args )
  {
    preg_match_all('/(?<=")[^"]*(?=")|(\w+)/i', $args, $result);
    return $result[0];
  }


  public function buildForm( $template, $context, $args, $default = array(), $hidden = null )
  {
    $props = $this->parseProps( $args, $context );
    $args  = $template->parseArguments( $args );

    $settings = ( $props ) 
                ? array_merge( $default, $props )
                : $default;

    $buffer = \Form::open( $settings );

    if( count( $args ) == 1 && $args[0] instanceof \Handlebars\String )
    {
      $buffer .= \Form::hidden('redirect', $this->getRouteUri( $args[0]->getString() ) );
    }

    if( is_array( $hidden ) )
    {
      foreach( $hidden as $fieldname => $value )
      {
        $buffer .= \Form::hidden( $fieldname, $value );
      }
    }

    $buffer .= $template->render( $context );
    $buffer .= "\n".'</form>'."\n";

    return $buffer;
  }

}
