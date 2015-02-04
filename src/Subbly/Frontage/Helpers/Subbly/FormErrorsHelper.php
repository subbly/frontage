<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class FormErrorsHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{#formErrors login}}
   *  <ul>
   *    {{#each errors}}
   *    <li>{{this}}</li>
   *    {{/each}}
   *  </ul>
   * {{/formErrors}}
   * Catch form validation's erros. 
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
    $args   = $template->parseArguments( $args );
    $errors = \Session::get('errors', new \Illuminate\Support\MessageBag);

    // no form specified
    // return empty buffer
    if( !count( $args ) )
      return $buffer;

    // if MessageBag does not exists
    // return empty buffer
    if( !method_exists( $errors, 'hasBag' ) )
      return $buffer;

    // Defined MessageBag exists
    // so we push errors list to the context
    if( $errors->hasBag( $args[0] ) )
    {
      $context->push( ['errors' => $errors->{$args[0]}->all() ] );
      $template->rewind();
      $buffer .= $template->render( $context );
      $context->pop();

      return $buffer;
    }

    // return empty buffer
    return $buffer;
  }
}
