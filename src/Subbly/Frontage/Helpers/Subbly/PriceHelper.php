<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class PriceHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{price 12.00}}
   * {{price 12.00 'USD'}}
   * {{price this 'USD'}}
   * {{price this}}
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
    $currencies   = \Config::get('currency');
    $buffer       = '';
    $args         = $template->parseArguments( $args );
    $countAgrs    = count( $args );
    $symbol_style = '%symbol%';

    if( $countAgrs == 0 )
      return $buffer;

    if( $args[0] == 'this' )
    {
      $product = $context->get('this');
      $value   = $product['price'];
    }
    else if( is_numeric( $args[0] ) )
    {
      $value   = $args[0];
    }
    
    if( $countAgrs === 1 )
    {
      $currency = $currencies['table'][ $currencies['default'] ];
    }
    else
    {
      $currency = ( isset( $currencies['table'][ $args[1]->getString() ] ) )
                  ? $currencies['table'][ $args[1]->getString() ]
                  : $currencies['table'][ $currencies['default'] ];
    }

    $symbol_left    = $currency['symbol_left'];
    $symbol_right   = $currency['symbol_right'];
    $decimal_place  = $currency['decimal_place'];
    $decimal_point  = $currency['decimal_point'];
    $thousand_point = $currency['thousand_point'];

    $string = '';

    if( $symbol_left ) 
    {
      $string .= str_replace( '%symbol%', $symbol_left, $symbol_style );
      
      if( $currencies['use_space'] )
        $string .= ' ';
    }

    $string .= number_format( round( $value, (int) $decimal_place ), (int) $decimal_place, $decimal_point, $thousand_point );

    if( $symbol_right ) 
    {
      if( $currencies['use_space'] )
        $string .= ' ';

      $string .= str_replace( '%symbol%', $symbol_right, $symbol_style );
    }

    return $string;
  }
}
