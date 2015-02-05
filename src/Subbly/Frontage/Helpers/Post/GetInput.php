<?php
namespace Subbly\Frontage\Helpers\Post;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class GetInput extends CustomHelper
{
  /**
   * Execute the helper
   * {{input 'fieldname'}}
   * {{input 'fieldname' default}}
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
    $args      = $template->parseArguments( $args );
    $countAgrs = count( $args );

    if( $countAgrs == 0 )
      return '';

    if( !$args[0] instanceof \Handlebars\String )
      return '';

    $old = \Input::old( $args[0]->getString(), false );

    if( $old )
      return $old;

    return \Input::get( $args[0]->getString(), $context->get( $args[0]->getString() )  );

  }
}
