<?php
namespace Subbly\Frontage\Helpers\Post;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;
use Subbly\Frontage\FrontageInvalidHelperException;

class AddToCartHelper extends CustomHelper
{
  /**
   * Execute the helper
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
    $default = array( 
        'route'    => 'frontage.form.addtocart'
    );

    $product = $context->get('this');

    if( !isset( $product['id'] ) && !isset( $product['id'] ) )
      throw new FrontageInvalidHelperException(
          '"AddToCar" helper need to be inside a "product".'
      );
    
    $props = $this->parseProps( $args, $context );
    $args  = $template->parseArguments( $args );

    $settings = ( $props ) 
                ? array_merge( $default, $props )
                : $default;

    $fields = '';

    if( count( $args ) == 1 && $args[0] instanceof \Handlebars\String )
    {
      $fields .= \Form::hidden('redirect', $this->getRouteUri( $args[0]->getString() ) );
    }

    $fields .= \Form::hidden('id', $product['id'] );

    $buffer = \Form::open( $settings );
    $buffer .= "\n".$fields."\n";
    $buffer .= $template->render( $context );
    $buffer .= "\n".'</form>'."\n";
    // $context->pop();

    return $buffer;
  }
}
