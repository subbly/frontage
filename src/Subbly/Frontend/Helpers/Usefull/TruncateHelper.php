<?php
namespace Subbly\Frontend\Helpers\Usefull;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontend\Helpers\CustomHelper;

class TruncateHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{truncate description}}
   * {{truncate description '...'}}
   * {{truncate 'Some long string' 'elipse'}}
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

    if( count( $args ) < 2 )
      throw new Error("Handlerbars Helper 'truncate' needs 2 parameters");

    $string = $context->get( $args[0] );
    $limit  = $context->get( $args[1] );

    $end    = ( isset( $args[2] ) )
              ? $args[2]->getString()
              : '...';

    return \Str::limit( $string, $limit, $end );
  }
}
