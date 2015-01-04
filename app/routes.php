<?php

Route::get('/', ['as'=>'home', 'uses'=>function() {
  $contests = Contest::hot();
  return View::make('home')->with(['contests'=>$contests]);
}]);

Route::get('/est/{contest_id}/{slug}', ['as'=>'contest.view', 'uses'=>function($contest_id) {
  $contest = Contest::find($contest_id);
  if(!$contest->can_view)
  {
    Session::put('r', Request::url());
    return Redirect::to($contest->login_url);
  }
  return View::make('contests.view')->with(['contest'=>$contest]);
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
    Session::put('success', "You have joined {$contest->title}.");
    return Redirect::to($candidate->canonical_url);
  }
  Session::put('error', "You can not join {$contest->title}.");
  return Redirect::to($contest->canonical_url);
}]);


Route::get('/facebook/authorize', ['as'=>'facebook.authorize', 'uses'=>function() {
  $code = Input::get( 'code' );
  $fb = OAuth::consumer( 'Facebook' );
  if ( !empty( $code ) ) {
    try
    {
      $token = $fb->requestAccessToken( $code );
      Auth::fb_login($token);
      Session::put('success', "Welcome, " . Auth::user()->full_name);
      return Redirect::to(Session::get('onsuccess'));
    } catch (OAuth\Common\Http\Exception\TokenResponseException $e) {
    }
  }
  if(Input::get('error')) {
    Session::put('warning', "You must connect with Facebook before continuing.");
    return Redirect::to(Session::get('oncancel'));
  }
  Session::put('onsuccess', Input::get('success', route('home')));
  Session::put('oncancel', Input::get('cancel', route('home')));
  $url = $fb->getAuthorizationUri();
  return Redirect::to( (string)$url );
}]);

Route::get('/vote/{id}', ['before'=>'auth', 'as'=>'candidates.vote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  $v = Auth::user()->vote_for($candidate);
  Session::put('success', "You voted for {$candidate->name}");
  return $v->id;
}]);

Route::get('/unvote/{id}', ['before'=>'auth', 'as'=>'candidates.unvote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  Auth::user()->unvote_for($candidate);
  Session::put('success', "Ok, you unvoted {$candidate->name}");
  return Redirect::to($contest->canonical_url);
}]);

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

Route::get("/{etag}/assets/{type}/{name}", 'AssetController@get');
Route::get("/images/{id}/{size}", ['as'=>'image.view', 'uses'=>'AssetController@image']);


Route::group([
  'prefix' => 'api/v1',
  'before'=>'origin',
], function() {
  Route::any('/my/contests', 'Api\V1\Controller@my_contests');
  Route::any('/contests/create', 'Api\V1\ContestEditController@create');
  Route::any('/contests/save', 'Api\V1\ContestEditController@save');
  Route::any('/contests/join', 'Api\V1\ContestViewController@join');
  Route::any('/user', 'Api\V1\UserController@get');
  Route::any('/vote', 'Api\V1\VoteController@vote');
  Route::any('/unvote', 'Api\V1\VoteController@unvote');
  Route::any('/contests/top', 'Api\V1\ContestController@top');
  Route::any('/contests/hot', 'Api\V1\ContestController@hot');
  Route::any('/contests/new', 'Api\V1\ContestController@recent');
  Route::any('/contests/local', 'Api\V1\ContestController@local');
  Route::any('/contests/{id}', 'Api\V1\ContestController@get');

  // Wildcard if no matched route
  Route::any( '/', function(  ){
    return ApiSerializer::ok();
  })->where('all', '.*');
  Route::any( '{all}', function( $uri ){
    return ApiSerializer::ok();
  })->where('all', '.*');
});

Route::get('/est/{contest_id}/{slug}/{user_id?}/{candidate_id?}', ['as'=>'contest.view', 'uses'=>'ContestViewController@view_old']);

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

Route::any('{all}', function($url) { 
 return View::make('app');
})->where('all', '.*');
