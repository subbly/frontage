<?php
namespace Subbly\Frontend\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontend\Helpers\CustomHelper;
use Subbly\Subbly;

class UserCheckHelper extends CustomHelper
{
  /**
   * Execute the helper
   * {{#isUserLogin}}
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
    $tmp = Subbly::api('subbly.user')->check();

    if( !$tmp )
    {
      $template->setStopToken('else');
      $buffer = $template->render($context);
      $template->setStopToken(false);
      $template->discard($context);
    }
    else
    {
      $context->push( ['user' => Subbly::api('subbly.user')->currentUser() ]);

      $template->setStopToken('else');
      $template->discard($context);
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }

    $context->pop();

    return $buffer;
  }
}
