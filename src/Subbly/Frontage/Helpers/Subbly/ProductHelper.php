<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

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
    $props  = $this->parseProps( $args, $context );
    $args   = $this->parseArgs( $args );
    $field  = 'id';
    $id     = false;
    $buffer = '';

    // no properties
    // so product's ID is in URL
    // or is th first arguments
    if( !$props )
    {
      $id = ( count( $args ) === 0 )
            ? $context->get('inputs.productId')
            : $args[0];
    }
    else
    {
      if( array_key_exists( 'productId', $props ) )
      {
        $id = $props['productId'];
      }
      else if( array_key_exists( 'productSku', $props ) )
      {
        $id    = $props['productSku'];
        $field = 'sku';
      }
    }

    if( !$id )
      throw new \InvalidArgumentException( 'Can not find product identifier');

    // Product query
    // ----------------

    // TODO: add status restriction if 
    // current user is not loggued to Backend    
    $productOptions = [
        'includes' => [ 'images', 'categories', 'options', 'translations' ]
      , 'where'    => [
            ['status', '!=', 'draft']
          , ['status', '!=', 'hidden']
        ]
    ];

    // Get product
    // -----------------

    try
    {
      $product = \Subbly\Subbly::api('subbly.product')->find( $id, $productOptions, $field )->toArray();
    }
    catch (\Exception $e)
    {
      throw new \InvalidArgumentException( $e->getMessage() );
    }

    $context->push($product);
    $template->rewind();
    $buffer .= $template->render($context);
    $context->pop();

    return $buffer;
  }
}
