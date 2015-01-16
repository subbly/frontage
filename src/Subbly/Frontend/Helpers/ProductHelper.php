<?php
namespace Subbly\Frontend\Helpers;

use \Handlebars\Context;
use \Handlebars\Helper;
use \Handlebars\Template;

class ProductHelper extends CustomHelper
{
  /**
   * Execute the helper
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
    $args  = $this->parseArgs( $args );

    $id = ( count( $args ) === 0 )
          ? $context->get('inputs.id')
          : $args[0];


    // Product query
    // ----------------
    
    $productOptions = [
        'includes' => [ 'images', 'categories', 'options' ]
      , 'where'    => [
            ['id', '=', $id]
          , ['status', '!=', 'draft']
          , ['status', '!=', 'hidden']
        ]
    ];

    // Get product
    // -----------------
    $product = \Subbly\Subbly::api('subbly.product')->all( $productOptions )->toArray();

    if( count( $product ) != 1 )
    {
      $tmp = false;
    }
    else
    {
      $context->push( [ 'product' => $product[0] ] );
      
      $tmp    = $product;      
    }

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
