<?php
namespace Subbly\Frontage\Helpers\Post;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;
use Subbly\Frontage\FrontageInvalidHelperException;

class CartUpdateQtyHelper extends CustomHelper
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
        'route'    => 'frontage.form.updatecart'
    );

    $cart = $context->get('this');

    if( !isset( $cart['rowid'] ) )
      throw new FrontageInvalidHelperException(
          '"carUpdateQty" helper need to be inside the "cart" helper.'
      );

    return $this->buildForm( $template, $context, $args, $default, [
      'cartRowId' => $cart['rowid']
    ]);
  }
}
