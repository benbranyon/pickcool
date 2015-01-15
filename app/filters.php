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
  //IP whitelist for next.pick.cool
  $ip = Request::getClientIp();
  $allowed = array('50.37.27.223', '192.168.254.27');
  if(Request::server('HTTP_HOST') != 'pick.cool'  && !in_array($ip, $allowed))
  {
    return Redirect::away('https://pick.cool');
  }

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
  if(Auth::user()) return;
  Session::put('onsuccess', Input::get('success', Request::url()));
  Session::put('oncancel', Input::get('cancel', Request::url()));
  return Redirect::to(r('login'));
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
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


Route::filter('origin', function($route, $request) {
  header('Access-Control-Allow-Origin: '.Request::header('Origin'));
});
