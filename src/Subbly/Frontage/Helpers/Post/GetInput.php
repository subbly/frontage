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

    $old     = \Input::old( $args[0]->getString(), false );
    $default = false;

    if( $old )
      return $old;

    if( $countAgrs == 2 )
      return \Input::get( $args[1]->getString(), false  );

    return '';
  }
}
