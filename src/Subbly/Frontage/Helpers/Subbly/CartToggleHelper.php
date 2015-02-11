<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;
use Subbly\Frontage\FrontageInvalidHelperException;

class CartToggleHelper extends CustomHelper
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
    $buffer = '';

    $cart = \Subbly\Subbly::api('subbly.cart')->count();

    if( !count( $cart ) ) 
    {
      $template->setStopToken('else');
      $template->discard($context);
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }
    else
    {
      $template->setStopToken('else');
      $buffer = $template->render($context);
      $template->setStopToken(false);
      $template->discard($context);
    }

    return $buffer;
  }
}
