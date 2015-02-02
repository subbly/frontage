<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;
use Subbly\Subbly;

class UserAddressHelper extends CustomHelper
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
    $isLogin = $context->get('isUserLogin');

    if( !$isLogin )
      throw new \Exception("User need to be log-in to access to his addresses");

    $props  = $this->parseProps( $args, $context );
    $args   = $this->parseArgs( $args );
    $id     = false;
    $buffer = '';

    // no properties
    // so address's ID is in URL
    // or is th first arguments
    if( !$props )
    {
      $id = ( count( $args ) === 0 )
            ? $context->get('inputs.addressId')
            : $args[0];
    }
    else
    {
      if( array_key_exists( 'addressId', $props ) )
        $id = $props['addressId'];
    }

    if( !$id )
      throw new \InvalidArgumentException( 'Can not find product identifier');

    // Get Address
    // -----------------
    $user      = $context->get('user');

    try
    {
      $options   = [
        'where' => [
          // prevent user to access
          // somebody else data
          ['uid', '=', $user->uid]
        ]
      ];

      $address = Subbly::api('subbly.user_address')->find($id, $options)->toArray();
    }
    catch( \Illuminate\Database\Eloquent\ModelNotFoundException $e )
    {
      throw new \InvalidArgumentException( $e->getMessage() );
    }

    $buffer = '';
    $context->push($address);
    $template->rewind();
    $buffer .= $template->render($context);
    $context->pop();

    return $buffer;
  }
}
