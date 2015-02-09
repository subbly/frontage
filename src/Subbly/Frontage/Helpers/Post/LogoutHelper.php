<?php
namespace Subbly\Frontage\Helpers\Post;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class LogoutHelper extends CustomHelper
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
      'route' => 'frontage.form.logout'
    );
    
    $props = $this->parseProps( $args, $context );
    $args  = $template->parseArguments( $args );

    $settings = ( $props ) 
                ? array_merge( $default, $props )
                : $default;

    $buffer = \Form::open( $settings );

    if( count( $args ) == 1 && $args[0] instanceof \Handlebars\String )
      $buffer .= \Form::hidden('redirect', $this->getRouteUri( $args[0]->getString() ) );

    $buffer .= $template->render( $context );
    $buffer .= "\n".'</form>'."\n";

    return $buffer;
  }
}
