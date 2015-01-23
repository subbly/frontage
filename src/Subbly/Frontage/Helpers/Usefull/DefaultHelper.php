<?php
namespace Subbly\Frontage\Helpers\Usefull;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class DefaultHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{default varable 'default string'}}
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
    $args = $template->parseArguments( $args );

    if( count( $args ) < 2 )
      throw new Error("Handlerbars Helper 'default' needs 2 parameters");

    $asked   = $context->get( $args[0] );
    $default = $context->get( $args[1] );

    return ( $asked ) 
           ? $asked
           : $default;
  }
}
