<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class CartTotalHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{CartTotalHelper}}
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

    // $args = $template->parseArguments( $args );

    // if( count( $args ) != 1 )
    //   throw new \InvalidArgumentException(
    //       '"assets" helper expects exactly one argument.'
    //   );

    return Subbly::api('subbly.cart')->total();
  }
}
