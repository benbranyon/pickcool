<?php

function r($route_name, $params=[], $absolute=true)
{
/*
  if($_ENV['USE_SSL'])
  {
    return preg_replace("/^http:/", "https:", route($route_name, $params, $absolute));
  }
*/

  return route($route_name, $params, $absolute);
}

/*
Grab a variable from the session, but validate it against an array of values or a callback
*/
function session_get($name, $values_or_callable=null)
{
  $v = Session::get($name);
  if(is_array($values_or_callable))
  {
    if(!in_array($v, $values_or_callable))
    {
      return $values_or_callable[0];;
    }
    return $v;
  }
  if(is_callable($values_or_callable))
  {
    return call_user_func($values_or_callable, $v);
  }
  if($v!==null) return $v;
  return $values_or_callable;
}

Route::get("/images/{id}/{size}", ['as'=>'image.view', 'uses'=>  function($id,$size) 
{
  $image = Image::find($id);
  if(!$image)
  {
    App::abort(404);
  }

  return Redirect::to($image->image->url($size), 301); 
}]);


Route::get('/', ['as'=>'home', 'uses'=>function() {
  //$contests = Contest::hot();
  //Session::flush();
  return View::make('landing');
  //return View::make('home')->with(['contests'=>$contests]);
}]);

Route::group(['prefix'=>'/est/{contest_id}/{contest_slug}'], function() {
  Route::group(['prefix'=>'/picks/{candidate_id}/{candidate_slug}'], function() {
    Route::get('/', ['as'=>'contests.candidate.view', 'uses'=>'CandidateController@view']);
    Route::get('/refresh', ['as'=>'contests.candidate.refresh', 'uses'=>'CandidateController@refresh']);
    Route::any('/images', ['as'=>'contests.candidates.images', 'uses'=>'CandidateImagesController@images']);
    Route::any('/images/add', ['as'=>'contests.candidates.images.add', 'uses'=>'CandidateImagesController@add']);
    Route::get('/manage/{cmd}/{cmd_id}', ['before'=>'auth', 'as'=>'contests.candidate.manage', 'uses'=>'CandidateManagerController@manage']);
  });

  Route::get('/{view_mode?}', ['as'=>'contest.view', 'uses'=>'ContestController@view']);
  Route::any('/login', ['before'=>['auth'], 'as'=>'contest.login', 'uses'=>'ContestController@login']);
});


Route::any('/join/{id}', ['before'=>'auth', 'as'=>'contest.join', 'uses'=>'ContestController@join']);



Route::get('/live/', ['as'=>'contest.live.view', 'uses'=>'LiveController@view']); 

Route::get('/join/{id}/done', ['before'=>'auth', 'as'=>'contests.candidates.after_join', 'uses'=>'JoinController@done']);


Route::get('/login', ['as'=>'login', 'uses'=>'AuthController@login']);
Route::get('/logout', ['as'=>'logout', 'uses'=>'AuthController@logout']);


Route::get('/facebook/authorize', ['as'=>'facebook.authorize', 'uses'=>'AuthController@authorize']);

Route::get('/facebook/authorize/retry', ['as'=>'facebook.authorize.retry', 'uses'=>function() {
  return View::make('facebook.authorize.retry');
}]);

Route::get('/vote/{id}', ['before'=>'auth', 'as'=>'candidates.vote', 'uses'=>'VoteController@vote']);

Route::get('/vote/{id}/done', ['before'=>'auth', 'as'=>'candidates.after_vote', 'uses'=>'VoteController@done']);


Route::get('/unvote/{id}', ['before'=>'auth', 'as'=>'candidates.unvote', 'uses'=>'VoteController@unvote']);

Route::get('/candidate/charity_boost/{id}', ['as'=>'candidate.charity_boost', 'uses'=>'Admin\CandidateController@charity_boost']);

Route::get('/sponsor/signup/{id}', ['before'=>'auth', 'as'=>'sponsors.signup', 'uses'=>'SponsorController@signup']);

Route::post('/sponsor/edit/{id}', ['as'=>'sponsors.edit', 'uses'=>'SponsorController@edit']);

Route::post('sponsor/create/', 'SponsorController@create');

Route::get('/hot', ['as'=>'contests.hot', 'uses'=>function() {
  $contests = Contest::hot();
  return View::make('home')->with(['contests'=>$contests, 'state'=>'hot']);
}]);
Route::get('/new', ['as'=>'contests.new', 'uses'=>function() {
  $contests = Contest::recent();
  return View::make('home')->with(['contests'=>$contests, 'state'=>'new']);
}]);
Route::get('/top', ['as'=>'contests.top', 'uses'=>function() {
  $contests = Contest::top();
  return View::make('home')->with(['contests'=>$contests, 'state'=>'top']);
}]);


Route::get('/tips', ['as'=>'tips', 'uses'=>function() {
  return View::make('tips');
}]);

Route::get('/unfollow/{contest_id}/{candidate_id}', ['as'=>'contests.candidate.unfollow', 'uses'=>function($contest_id, $candidate_id) {
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

Route::get('/faq', ['as'=>'faq', 'uses'=>function() {
  return View::make('legal.faq');
}]);

Route::get('/privacy', ['as'=>'privacy', 'uses'=>function() {
  return View::make('legal.privacy');
}]);

Route::get('/terms', ['as'=>'terms', 'uses'=>function() {
  return View::make('legal.terms');
}]);

Route::get('/inbox', ['before'=>'auth', 'as'=>'inbox', 'users'=>function() {
  return View::make('inbox.list', ['messages'=>Auth::user()->messages]);
}]);

Route::get('/inbox/{message_id}/read', ['before'=>'auth', 'as'=>'inbox.read', 'uses'=>function($message_id) {
  $message = Message::find($message_id);
  if(!$message)
  {
    App::abort(404);
  }
  $message->read_at = Carbon::now();
  $message->save();
  return View::make('inbox.read', ['message'=>$message]);
}]);

// Admin Routes
Route::group(array('prefix'=> 'admin', 'before' => ['auth.admin'],['forceHttps']), function() {

    Route::get('/', array('uses' => 'Admin\\DashboardController@index', 'as' => 'admin.home'));

    Route::get('images', ['as'=>'admin.images', 'uses'=>'Admin\\ImageController@index']);
    Route::get('images/{image_id}/{status}', ['as'=>'admin.images.status', 'uses'=>'Admin\\ImageController@set_status']);

    Route::get('badges', ['as'=>'admin.badges', 'uses'=>'Admin\\BadgeController@index']);
    
    // Resource Controller for user management, nested so it needs to be relative
    Route::resource('users', 'Admin\\UserController');

    Route::resource('contests', 'Admin\\ContestController');
    Route::resource('contests/{id}/edit/', 'Admin\\ContestController@edit');

    Route::resource('candidates', 'Admin\\CandidateController');
    Route::resource('candidates/{id}/edit/', 'Admin\\CandidateController@edit');

    Route::resource('sponsors', 'Admin\\SponsorController');
    Route::resource('sponsors/{id}/edit/', 'Admin\\SponsorController@edit');

});

Route::get('/userheader.js', ['uses'=>function() {
  return Response::view('userheader-js')->header('Content-Type', 'application/javascript');
}]);
