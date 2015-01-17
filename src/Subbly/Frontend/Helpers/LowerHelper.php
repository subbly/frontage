<?php
namespace Subbly\Frontend\Helpers;

use \Handlebars\Context;
use \Handlebars\Helper;
use \Handlebars\Template;

class LowerHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{upper 'some word'}}
   * {{upper name}}
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

    return mb_strtolower( $context->get( $args[0] ) );
  }
}
