<?php
namespace Subbly\Frontend\Helpers;

use \Handlebars\Context;
use \Handlebars\Helper;
use \Handlebars\Template;

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
      return $buffer;

    $themePath = $context->get('themes');

    return $themePath . DS . $args[0]->getString();
  }
}
