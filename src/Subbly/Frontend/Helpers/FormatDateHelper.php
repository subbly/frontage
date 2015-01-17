<?php
namespace Subbly\Frontend\Helpers;

use \Handlebars\Context;
use \Handlebars\Helper;
use \Handlebars\Template;

class FormatDateHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{formatDate created_at 'd M'}}
   * {{formatDate '2014-11-18T20:10:54+0000' 'Y-m-d'}}
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

    if( count( $args) != 2 )
      return $buffer;

    $date = ( $args[0] instanceof \Handlebars\String )
            ? $args[0]->getString()
            : $context->get( $args[0] );

    $format  = $args[1]->getString();

    if( $format )
    {
        $dt = new \DateTime;

        if( is_numeric( $date ) )
        {
          $dt = ( new \DateTime )->setTimestamp( $date );
        }
        else
        {
          $dt = new \DateTime( $date );
        }

        return $dt->format( $format );
    }
    else
    {
      return $date;
    }
  }
}
