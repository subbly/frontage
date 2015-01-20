<?php

namespace Subbly\Frontend\Controllers;

use Subbly\Subbly;
use Input;

class Login
  extends Action 
{
  /**
   * Validations
   */
  protected $rules = array(
      'email'    => 'required|email'
    , 'password' => 'required'
  );

  public function run()
  {
    $this->formValidator( $this->rules );

    try 
    {
      $credentials   = array(
          'login'    => Input::get('email')
        , 'password' => Input::get('password')
      );

      $authenticated = Subbly::api('subbly.user')
                          ->authenticate( $credentials );
    }
    catch( \Exception $e )
    {
      if( in_array( get_class( $e ), array(
          'Cartalyst\\Sentry\\Users\\UserNotActivatedException',
          'Cartalyst\\Sentry\\Users\\UserSuspendedException',
          'Cartalyst\\Sentry\\Users\\UserBannedException',
      ))) 
      {
dd('user');        
        return $this->errorResponse($e->getMessage());
      }
      else if( in_array( get_class( $e ), array(
          'Cartalyst\\Sentry\\Users\\LoginRequiredException',
          'Cartalyst\\Sentry\\Users\\PasswordRequiredException',
          'Cartalyst\\Sentry\\Users\\WrongPasswordException',
          'Cartalyst\\Sentry\\Users\\UserNotFoundException',
      ))) 
      {
dd('validation', $e->getMessage());
        return $this->errorResponse('Auth required! Something is wrong with your credentials.', 401);
      }
      dd('fatal');
      return $this->errorResponse('FATAL ERROR!', 500);
    }
      return \Redirect::back();
dd('ok');
  }
}
