<?php
namespace BenAllfree\FbAuth;
  
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
  
  public static function fb_login($me)
  {
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