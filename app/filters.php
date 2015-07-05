<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
});

App::after(function($request, $response)
{
	$log = new ActivityLog();
	$log->save();
});


App::before(function($request, $response)
{
  if(!env('IP_WHITELIST',false)) return;
  $allowed = explode(',', env('IP_WHITELIST'));
  $ips = array_map('trim', explode(',', Request::server('HTTP_X_FORWARDED_FOR', Request::getClientIp())));
  if(!array_intersect($allowed, $ips))
  {
    if(Request::server('HTTP_HOST') != 'pick.cool'  && !in_array($ip, $allowed))
    {
      return Redirect::away('https://pick.cool');
    }
  }
});

App::before(function($request, $response)
{
  if(!$_ENV['USE_SSL']) return;
  if(Request::server('HTTP_X_FORWARDED_PROTO')=='https') return;
  $url = 'https://'.Request::server('HTTP_HOST').Request::server('REQUEST_URI');
  return Redirect::to($url, 301);
});


/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
  if(Input::get('fb_access_token'))
  {
    Auth::fb_login(Input::get('fb_access_token'));
  }
  
  if(Auth::user())
  {
  	$fb = OAuth::consumer( 'Facebook' );

  	try {
  		$me = json_decode( $fb->request( '/me' ), true );
  	}
  	catch(Exception $e)
  	{
  		$me = false;
  	}

  	if($me)
  	{
  		return;
  	}
  }
  Session::put('onsuccess', Input::get('success', Request::url()));
  Session::put('oncancel', Input::get('cancel', Request::url()));
  return Redirect::to(r('login'));
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('auth.admin', function($route, $request){
    // Check if the user is logged in, if not redirect to login url
    if (Auth::guest()) return Redirect::guest('/facebook/authorize');

    if(Auth::user()->is_admin)
    {
      return;
    }

    return Redirect::to(r('contests.hot'));// home
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('forceHttps', function($req){
    if($_ENV['USE_SSL'])
  {
    if (! Request::secure()) {
        return Redirect::secure(Request::getRequestUri());
    }
  }
});

Route::filter('origin', function($route, $request) {
  header('Access-Control-Allow-Origin: '.Request::header('Origin'));
});
