<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Subbly as Subbly;
use Subbly\Frontage\Helpers\CustomHelper;

class CartHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{CartHelper}}
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
    $buffer = '';

    $cart = \Subbly\Subbly::api('subbly.cart')->content()->toArray();

    if( !$cart) 
    {
      $template->setStopToken('else');
      $template->discard();
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }
    elseif( is_array( $cart ) || $cart instanceof \Traversable )
    {
      $isList = is_array($cart) && (array_keys($cart) === range(0, count($cart) - 1));
      $index = 0;
      $lastIndex = $isList ? (count($cart) - 1) : false;

      foreach( $cart as $key => $var ) 
      {
        $specialVariables = array(
            '@index' => $index,
            '@first' => ($index === 0),
            '@last' => ($index === $lastIndex),
        );
        if (!$isList) {
            $specialVariables['@key'] = $key;
        }
        $context->pushSpecialVariables($specialVariables);
        $context->push($var);
        $template->setStopToken('else');
        $template->rewind();
        $buffer .= $template->render($context);
        $context->pop();
        $context->popSpecialVariables();
        $index++;
      }

      $template->setStopToken(false);
    }

    return $buffer;
  }
}
