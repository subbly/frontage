<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class AssetsHelper extends CustomHelper
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

    $args = $template->parseArguments( $args );

    if( count( $args ) != 1 )
      throw new \InvalidArgumentException(
          '"assets" helper expects exactly one argument.'
      );

    // Prevent `extends` sub level 
    if( is_null( $themePath = $context->get('themes') ) )
      $themePath = $context->get('../themes');

    return $themePath . DS . $args[0]->getString();
  }
}
