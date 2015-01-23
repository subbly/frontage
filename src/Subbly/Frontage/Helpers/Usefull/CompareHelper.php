<?php
namespace Subbly\Frontage\Helpers\Usefull;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class CompareHelper extends CustomHelper
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

    if( count( $args ) < 2 )
      throw new Error("Handlerbars Helper 'compare' needs 2 parameters");

    $operator = ( isset( $args[2] ) ) ? $args[2]->getString() : '==';
    $lvalue   = $context->get( $args[0] );
    $rvalue   = $context->get( $args[1] );

    $equal       = function($l,$r) { return $l == $r; };
    $strictequal = function($l,$r) { return $l === $r; };
    $different   = function($l,$r) { return $l != $r; };
    $lower       = function($l,$r) { return $l < $r; };
    $greatter    = function($l,$r) { return $l > $r; };
    $lowOrEq     = function($l,$r) { return $l <= $r; };
    $greatOrEq   = function($l,$r) { return $l >= $r; };

    $operators = [
        '=='  => 'equal'
      , '===' => 'strictequal'
      , '!='  => 'different'
      , '<'   => 'lower'
      , '>'   => 'greatter'
      , '<='  => 'lowOrEq'
      , '>='  => 'greatOrEq'
    ];

    if (!$operators[ $operator ] )
        throw new Error("Handlerbars Helper 'compare' doesn't know the operator " . $operator );

    $tmp    = $$operators[ $operator ]( $lvalue, $rvalue );
    $buffer = '';

    $context->push($context->last());

    if( $tmp )
    {
      $template->setStopToken('else');
      $buffer = $template->render($context);
      $template->setStopToken(false);
      $template->discard($context);
    }
    else
    {
      $template->setStopToken('else');
      $template->discard($context);
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }
    
    $context->pop();

    return $buffer;
  }
}
