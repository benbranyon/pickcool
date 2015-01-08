<?php

Route::get('/', ['as'=>'home', 'uses'=>function() {
  $contests = Contest::hot();
  return View::make('home')->with(['contests'=>$contests]);
}]);

Route::get('/est/{contest_id}/{slug}', ['as'=>'contest.view', 'uses'=>function($contest_id) {
  $contest = Contest::find($contest_id);
  if(!$contest->can_view)
  {
    return Redirect::to($contest->login_url);
  }
  if(Input::get('s'))
  {
    Session::put('contest_view_mode', Input::get('s'));
  }
  return View::make('contests.view')->with(['contest'=>$contest]);
}]);

Route::get('/est/{contest_id}/{slug}/realtime', ['as'=>'contest.realtime', 'uses'=>function($contest_id) {
  $contest = Contest::find($contest_id);
  if(!$contest->can_view)
  {
    return Redirect::to($contest->login_url);
  }
  if(Input::get('s'))
  {
    Session::put('contest_view_mode', Input::get('s'));
  }
  return View::make('contests.realtime')->with(['contest'=>$contest]);
}]);

Route::get('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}', ['as'=>'contest.candidate.view', 'uses'=>function($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
  $contest = Contest::find($contest_id);
  if(!$contest->can_view)
  {
    Session::put('r', Request::url());
    return Redirect::to($contest->login_url);
  }
  $candidate = Candidate::find($candidate_id);
  if(Auth::user() && Input::get('v'))
  {
    Auth::user()->vote_for($candidate);
  }
  return View::make('candidates.view')->with(['contest'=>$contest, 'candidate'=>$candidate]);
}]);


Route::any('/est/{contest_id}/{slug}/login', ['before'=>['auth'], 'as'=>'contest.login', 'uses'=>function($contest_id) {
  $contest = Contest::find($contest_id);
  if($contest->can_view)
  {
    return Redirect::to($contest->canonical_url);
  }
  if(Input::get('password'))
  {
    $pw = trim(Input::get('password'));
    if($pw == $contest->password)
    {
      $contest->authorize_user();
      return Redirect::to($contest->canonical_url);
    }
  }
  return View::make('contests.login')->with(['contest'=>$contest]);
}]);

Route::get('/join/{id}', ['before'=>'auth', 'as'=>'contest.join', 'uses'=>function($id) {
  $contest = Contest::find($id);
  if($contest->can_join || $contest->has_joined)
  {
    $candidate = $contest->add_user();
    return Redirect::to($candidate->after_join_url);
  }
  Session::put('error', "You can not join {$contest->title}.");
  return Redirect::to($contest->canonical_url);
}]);

Route::get('/join/{id}/done', ['before'=>'auth', 'as'=>'candidates.after_join', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  return View::make('contests.after_join')->with(['candidate'=>$candidate, 'contest'=>$contest]);
}]);



Route::get('/facebook/authorize', ['as'=>'facebook.authorize', 'uses'=>function() {
  $code = Input::get( 'code' );
  $fb = OAuth::consumer( 'Facebook' );
  if ( !empty( $code ) ) {
    try
    {
      $token = $fb->requestAccessToken( $code );
      try
      {
        Auth::fb_login($token);
      } catch (Exception $e) {
        Session::put('fb_retry', true);
        return Redirect::to(route('facebook.authorize.retry'));
      }
      Session::put('success', "Welcome, " . Auth::user()->full_name);
      return Redirect::to(Session::get('onsuccess'));
    } catch (OAuth\Common\Http\Exception\TokenResponseException $e) {
    }
  }
  if(Input::get('error')) {
    Session::put('warning', "You must connect with Facebook before continuing.");
    return Redirect::to(Session::get('oncancel'));
  }
  Session::put('onsuccess', Input::get('success', Session::get('onsuccess', route('home'))));
  Session::put('oncancel', Input::get('cancel', Session::get('oncancel', route('home'))));
  $params = [];
  if(Session::get('fb_retry'))
  {
    Session::forget('fb_retry');
    $params['auth_type'] = 'rerequest';
  }
  $url = $fb->getAuthorizationUri($params);
  return Redirect::to( (string)$url );
}]);

Route::get('/facebook/authorize/retry', ['as'=>'facebook.authorize.retry', 'uses'=>function() {
  return View::make('facebook.authorize.retry');
}]);

Route::get('/vote/{id}', ['before'=>'auth', 'as'=>'candidates.vote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  list($result,$v) = Auth::user()->vote_for($candidate);
  $qs = [
    'v'=>$result,
  ];
  $qs = http_build_query($qs);
  return Redirect::to($candidate->after_vote_url."?{$qs}");
}]);

Route::get('/vote/{id}/done', ['before'=>'auth', 'as'=>'candidates.after_vote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  return View::make('candidates.after_vote')->with(['candidate'=>$candidate, 'contest'=>$contest]);
}]);


Route::get('/unvote/{id}', ['before'=>'auth', 'as'=>'candidates.unvote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  Auth::user()->unvote_for($candidate);
  Session::put('success', "Ok, you unvoted {$candidate->name}");
  return Redirect::to($contest->canonical_url);
}]);

Route::get('/sponsor/signup/{id}', ['before'=>'auth', 'as'=>'sponsors.signup', 'uses'=>function($id) {
  $contest = Contest::find($id);
  $fb = \OAuth::consumer( 'Facebook' );
  $has_token = $fb->getStorage()->hasAccessToken("Facebook");
  
  //\Session::flush();
  //$fb->getStorage()->clearToken("Facebook");

  if(!$has_token || !Auth::user())
  {
    \Session::flush();

    $code = Input::get( 'code' );
    $fb = OAuth::consumer( 'Facebook' );
    if ( !empty( $code ) ) {
      try
      {
        $token = $fb->requestAccessToken( $code );
        try
        {
          Auth::fb_login($token);
        } catch (Exception $e) {
          Session::put('fb_retry', true);
          return Redirect::to(route('facebook.authorize.retry'));
        }
        Session::put('success', "Welcome, " . Auth::user()->full_name);
        return Redirect::to(Session::get('onsuccess'));
      } catch (OAuth\Common\Http\Exception\TokenResponseException $e) {
      }
    }
    if(Input::get('error')) {
      Session::put('warning', "You must connect with Facebook before continuing.");
      return Redirect::to(Session::get('oncancel'));
    }
    Session::put('onsuccess', Input::get('success', Session::get('onsuccess', route('sponsors.signup', $id))));
    Session::put('oncancel', Input::get('cancel', Session::get('oncancel', route('home'))));
    $params = [];
    if(Session::get('fb_retry'))
    {
      Session::forget('fb_retry');
      $params['auth_type'] = 'rerequest';
    }
    $params = [];
    $params['auth_type'] = 'rerequest';
    $url = $fb->getAuthorizationUri($params);
    return Redirect::to( (string)$url );  
  }

  try
  {
    $fb_token = $fb->getStorage()->retrieveAccessToken("Facebook"); 
    $access_token = $fb_token->getAccessToken();
    $client = new \GuzzleHttp\Client();
    $fb_permissions =  $client->get('https://graph.facebook.com/me/permissions?access_token=' . $access_token); 
    $fb_permissions = $fb_permissions->json();
  }
  catch (OAuth\Common\Http\Exception\TokenResponseException $e) {
    //Old token
    //$fb->getStorage()->clearToken("Facebook");
  }

  $user_photos = false;
  foreach($fb_permissions['data'] as $permission)
  {
    if(array_search('user_photos', $permission))
    {
      $user_photos = true;
    }
  }
  if(!$user_photos)
  {
    $dialog = 'https://www.facebook.com/dialog/oauth?client_id='. $_ENV['FACEBOOK_APP_ID']. '&redirect_uri=' .Request::root() .'/sponsor/signup/'.$id.'&scope=user_photos';
    return Redirect::to( (string) $dialog);
  }
  return View::make('sponsors.signup')->with(['contest'=>$contest]);
}]);

Route::post('/sponsor/edit/{id}', ['as'=>'sponsors.edit', 'uses'=>function($id) {
  return "hello";
}]);

Route::post('sponsor/create/', 'SponsorController@create');

Route::get('/contests/{id}/edit', ['as'=>'contests.edit', 'uses'=>function() {
  return "hi";
}]);

Route::get('/hot', ['as'=>'contests.hot', 'uses'=>function() {
  $contests = Contest::hot();
  return View::make('home')->with(['contests'=>$contests]);
}]);
Route::get('/new', ['as'=>'contests.new', 'uses'=>function() {
  $contests = Contest::recent();
  return View::make('home')->with(['contests'=>$contests]);
}]);
Route::get('/top', ['as'=>'contests.top', 'uses'=>function() {
  $contests = Contest::top();
  return View::make('home')->with(['contests'=>$contests]);
}]);



Route::get('/unfollow/{contest_id}/{candidate_id}', ['as'=>'contest.candidate.unfollow', 'uses'=>function($contest_id, $candidate_id) {
  return "You are no longer following {$c->contest->name} and will not receive any further updates about it.";
}]);

Route::get('/sponsors/{sponsor_id}', ['as'=>'sponsor', 'uses'=>function($sponsor_id) {
  $sponsor = Sponsor::find($sponsor_id);
  if(!$sponsor)
  {
    App::abort(404);
  }
  return Redirect::to($sponsor->url);
}]);


Route::get('/shop/{candidate_id}', ['buy', function($candidate_id) {
  $candidate = Candidate::find($candidate_id);
  if(!$candidate)
  {
    App::abort(404);
  }
  
  return Redirect::to($candidate->buy_url);
}]);
