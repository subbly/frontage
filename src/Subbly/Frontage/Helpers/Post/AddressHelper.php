<?php
namespace Subbly\Frontage\Helpers\Post;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class AddressHelper extends CustomHelper
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
      'route' => 'subbly.postcontroller.address'
    );

    // edit recorded address
    // ------------------------

    $props = $this->parseProps( $args, $context );
    $args  = $template->parseArguments( $args );

    $settings = ( $props ) 
                ? array_merge( $default, $props )
                : $default;

    $html  = \Form::open( $settings );
    $html .= \Form::hidden('addressId', $context->get('inputs.addressId') );

    return $html;
  }
}
