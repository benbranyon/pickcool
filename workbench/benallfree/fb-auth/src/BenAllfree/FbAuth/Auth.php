<?php
namespace BenAllfree\FbAuth;
  
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
  
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
  
  public static function user()
  {
    if(self::$user !== false) return self::$user;
    
    FacebookSession::setDefaultApplication(\Config::get('fb-auth::config.facebook_app_id'), \Config::get('fb-auth::config.facebook_secret'));

    $token = \Input::get('accessToken');
    if(!$token)
    {
      $token = \Request::header('FB-Access-Token');
    }
    if(!$token)
    {
      self::$user = null;
      return null;
    }
    
    $session = new FacebookSession($token);
    
    try {
      $me = (new FacebookRequest(
        $session, 'GET', '/me'
      ))->execute()->getGraphObject(GraphUser::className());
      self::$user = \User::from_fb($me);
    } catch (FacebookAuthorizationException $e) {
      self::$user = null;
    } catch (FacebookRequestException $e) {
      self::$user = null;
    } catch (\Exception $e) {
      self::$user = null;
    }
    
    return self::$user;
    
  }
}