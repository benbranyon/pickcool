<?php
namespace BenAllfree\FbAuth;

use OAuth\OAuth2\Token\StdOAuth2Token;

  
class Auth
{
  static  $user = false;
  
  public static function guest()
  {
    return !self::user();
  }
  
  public static function check()
  {
    return !self::guest();
  }
  
  public static function fb_login($access_token)
  {
    $fb = \OAuth::consumer( 'Facebook' );
    $token = $access_token;
    if(is_string($token))
    {
      $token = new StdOAuth2Token();
      $token->setAccessToken($access_token);
    }
    $fb->getStorage()->storeAccessToken("Facebook", $token);
    $has_token = $fb->getStorage()->hasAccessToken("Facebook");
    $me = json_decode( $fb->request( '/me' ), true );
    self::$user = \User::from_fb($me);
    \Session::put('user_id', self::$user->id);
    return self::$user;
  }
  
  public static function user()
  {
    if(self::$user !== false) return self::$user;

    $user_id = \Session::get('user_id');
    if(!$user_id) return null;
    
    return self::$user = \User::find($user_id);
  }
}