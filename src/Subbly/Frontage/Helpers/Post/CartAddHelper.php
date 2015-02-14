<?php
namespace Subbly\Frontage\Helpers\Post;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;
use Subbly\Frontage\FrontageInvalidHelperException;

class CartAddHelper extends CustomHelper
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
    $default = array( 
        'route'    => 'frontage.form.addtocart'
    );

    $product = $context->get('this');

    if( !isset( $product['id'] ) && !isset( $product['id'] ) )
      throw new FrontageInvalidHelperException(
          '"AddToCar" helper need to be inside a "product".'
      );

    return $this->buildForm( $template, $context, $args, $default, [
      'id' => $product['id']
    ]);
  }
}
