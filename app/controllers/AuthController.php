<?php

class AuthController extends \BaseController {
  function login() {
    Session::put('onsuccess', Input::get('success', Session::get('onsuccess', Request::url())));
    Session::put('oncancel', Input::get('cancel', Session::get('oncancel', Request::url())));
    return View::make('login');
  }

  function logout() {
    Session::flush();
    Session::put('success', 'You have been logged out.');
    return Redirect::to(r('home'));
  }
    
  function authorize() {
    $code = Input::get( 'code' );
    $fb = OAuth::consumer( 'Facebook' );
    if ( !empty( $code ) ) {
      try
      {
        $token = $fb->requestAccessToken( $code );
        //Trade for long-lived token
        $long_token_url = 'https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id='. $_ENV['FACEBOOK_APP_ID'] .'&client_secret=' . $_ENV['FACEBOOK_SECRET'] . '&fb_exchange_token='.$token->getAccessToken(); 
        $client = new \GuzzleHttp\Client();
        $response = $client->get($long_token_url);
        $long_token = $response->getBody()->__toString();
        try
        {
          Auth::fb_login($token);
        } catch (Exception $e) {
          Session::put('fb_retry', true);
          return Redirect::to(r('facebook.authorize.retry'));
        }
        Session::put('success', "Welcome, " . Auth::user()->full_name);
        $onsuccess=Session::get('onsuccess');
        Session::forget('onsuccess');
        Session::forget('oncancel');
        return Redirect::to($onsuccess);
      } catch (OAuth\Common\Http\Exception\TokenResponseException $e) {
      }
    }
    if(Input::get('error')) {
      Session::put('warning', "You must connect with Facebook before continuing.");
      $oncancel = Session::get('oncancel');
      Session::forget('onsuccess');
      Session::forget('oncancel');
      return Redirect::to($oncancel);
    }
    $params = [];
    if(Session::get('fb_retry'))
    {
      Session::forget('fb_retry');
      $params['auth_type'] = 'rerequest';
    }
    $url = $fb->getAuthorizationUri($params);
    return Redirect::to( (string)$url );
  }

}
