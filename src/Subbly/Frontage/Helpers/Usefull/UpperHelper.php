<?php
namespace Subbly\Frontage\Helpers\Usefull;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class UpperHelper extends CustomHelper
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

    if( $args[0] instanceof \Handlebars\String )
    {
      return mb_strtoupper( $args[0]->getString() );
    }
    else
    {
      return mb_strtoupper( $context->get( $args[0] ) );
    }
  }
}
