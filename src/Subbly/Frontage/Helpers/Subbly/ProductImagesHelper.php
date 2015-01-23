<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class ProductImagesHelper extends CustomHelper
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
    $buffer = '';

    $product = $context->get('this');

    if( 
        !isset( $product['images'] ) 
      )
    {
      return $buffer;
    }

    $tmp    = $product['images'];
    $buffer = '';

    if( !$tmp) 
    {
      $template->setStopToken('else');
      $template->discard();
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }
    elseif( is_array( $tmp ) || $tmp instanceof \Traversable )
    {
      $isList = is_array($tmp) && (array_keys($tmp) === range(0, count($tmp) - 1));
      $index = 0;
      $lastIndex = $isList ? (count($tmp) - 1) : false;

      foreach( $tmp as $key => $var ) 
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
