<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;
use Subbly\Subbly;

class UserAddressesHelper extends CustomHelper
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
    // $args = $this->parseArgs( $args );
    $user = $context->get('user');

    if( !$user )
      throw new \Exception("User need to be log-in to access to his addresses");

    // Get Address
    // -----------------
    
    $options   = [
      'where' => [
        // prevent user to access
        // somebody else data
        ['uid', '=', $user->uid]
      ]
    ];

    try
    {
      $addresses = Subbly::api('subbly.user_address')->findByUser($user, $options);
    }
    catch( \Illuminate\Database\Eloquent\ModelNotFoundException $e )
    {
      return false;
    }

    if( !count( $addresses ) )
    {
      $addresses = false;
    }

    $buffer = '';

    if( !$addresses) 
    {
      $template->setStopToken('else');
      $template->discard();
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }
    elseif( is_array( $addresses ) || $addresses instanceof \Traversable )
    {
      $isList = is_array($addresses) && (array_keys($addresses) === range(0, count($addresses) - 1));
      $index = 0;
      $lastIndex = $isList ? (count($addresses) - 1) : false;

      foreach( $addresses as $key => $var ) 
      {
        $specialVariables = array(
            '@index' => $index,
            '@first' => ($index === 0),
            '@last' => ($index === $lastIndex),
        );
        if (!$isList) {
            $specialVariables['@key'] = $key;
        }
        $context->pushSpecialVariables($specialVariables);
        $context->push($var);
        $template->setStopToken('else');
        $template->rewind();
        $buffer .= $template->render($context);
        $context->pop();
        $context->popSpecialVariables();
        $index++;
      }

      $template->setStopToken(false);
    }

    return $buffer;
  }
}
