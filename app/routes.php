<?php

use Carbon\Carbon;
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


Route::get('/my/set_visible', ['as'=>'my.set_visible', 'uses'=>'ProfileController@set_visible']);
Route::get('/api/profile/settings', ['as'=>'api.profile.settings', 'uses'=>'ProfileController@settings']);
Route::get('/profiles/{id}', ['as'=>'profile', 'uses'=>'ProfileController@home']);

Route::get('/', ['as'=>'home', 'uses'=>'HomeController@home']);

Route::group(['prefix'=>'/est/{contest_id}/{contest_slug}'], function() {
  Route::group(['prefix'=>'/picks/{candidate_id}/{candidate_slug}'], function() {
    Route::get('/', ['as'=>'contests.candidate.view', 'uses'=>'CandidateController@view']);
    Route::get('/voters', ['as'=>'contests.candidate.voters.view', 'uses'=>'CandidateController@view_voters']);
    Route::get('/refresh', ['as'=>'contests.candidate.refresh', 'uses'=>'CandidateController@refresh']);
    Route::any('/images', ['as'=>'contests.candidates.images', 'uses'=>'CandidateImagesController@images']);
    Route::any('/images/add', ['as'=>'contests.candidates.images.add', 'uses'=>'CandidateImagesController@add']);
    Route::get('/manage/{cmd}/{cmd_id}', ['before'=>'auth', 'as'=>'contests.candidate.manage', 'uses'=>'CandidateManagerController@manage']);
  });

  Route::get('/{view_mode?}', ['as'=>'contest.view', 'uses'=>'ContestController@view']);
  Route::any('/login', ['before'=>['auth'], 'as'=>'contest.login', 'uses'=>'ContestController@login']);
});


//Route::any('/join/{id}', ['before'=>'auth', 'as'=>'contest.join', 'uses'=>'ContestController@join']);
Route::any('/join/{id}', ['before'=>'auth', 'as'=>'contest.join', 'uses'=>function($id) {
  $contest = Contest::find($id);
  if(!$contest)
  {
    App::abort(404);
  }
  if($contest->is_ended)
  {
    Session::put('danger', "Sorry, voting has ended.");
    return Redirect::to($contest->canonical_url);
  }
  $state = Input::get('s','begin');
  if(!$contest->is_joinable)
  {
    Session::put('danger', "You can not join {$contest->title}.");
    return Redirect::to($contest->canonical_url);
  }
  $with = [
    'contest'=>$contest,
    'state'=>$state,
  ];
  $image_id = Input::get('img');
  if($image_id)
  {
    $image = Image::find($image_id);
    if(!$image)
    {
      Session::put('warning', 'Please upload an image.');
      return Redirect::to(r('contest.join', [$contest->id, 's'=>'picture']));
    }
    $with['image']=$image;
  }
  $candidate_id = Input::get('c');
  if($candidate_id)
  {
    $candidate = Candidate::find($candidate_id);
    if(!$candidate)
    {
      Session::put('warning', 'An unexpected error ocurred.');
      return Redirect::to(r('contest.join'));
    }
    $with['candidate']=$candidate;
  }  
  
  if($state=='picture' && Request::isMethod('post'))
  {
    $file = Input::file('image');
    if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
    {
      Session::put('danger', 'Please upload an image file before continuing.');
    } else {
      try
      {
        $image = new Image();
        $image->image = $file;
        $image->save();
        return Redirect::to(r('contest.join', [$contest->id, 's'=>'preview', 'img'=>$image->id]));
      } catch (Exception $e)
      {
        Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
      }
    }
  }
  if($state == 'bands')
  {
    if (Request::isMethod('post'))
    {
      $rules = array(
          'name' => array('required'),
          'music_url'       => array('required', 'url')
      );

      $validation = Validator::make(Input::all(), $rules);

      if ($validation->fails())
      {
          // Validation has failed.
          Session::put('danger', 'Please enter your name, image, and valid music url including the http://.');
          return Redirect::to(r('contest.join', [$contest->id, 's'=>'bands']))->withErrors($validation);
      }
      $file = Input::file('image');
      $name = Input::get('name');
      $music_url = Input::get('music_url');
      $bio = Input::get('bio');
      $youtube_url = Input::get('youtube_url');
      if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
      {
        Session::put('danger', 'Please upload an image file before continuing.');
      } else {
        try
        {
          $image = new Image();
          $image->image = $file;
          $image->save();

        } catch (Exception $e)
        {
          Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
        }
          $user = Auth::user();
          if(!$user) return null;
          $can = new Candidate();
          $can->contest_id = $contest->id;
          $can->user_id = $user->id;
          $can->name = $name;
          $can->music_url = $music_url;
          $can->youtube_url = $youtube_url;
          $can->bio = $bio;
          $can->save();
          $can = Candidate::find($can->id);
          Message::create([
            'user_id'=>$user->id,
            'subject'=>"Pick joined - pending verification",
            'body'=>View::make('messages.join_verify')->with([
              'candidate'=>$can,
              'contest'=>$can->contest,
            ]),
          ]);
          $user->vote_for($can);

          $image->candidate_id = $can->id;
          $image->save();
          return Redirect::to($can->canonical_url);
      }
    }
  }
  if($state=='finalize')
  {
    $candidate = $contest->add_user();
    $image->candidate_id = $candidate->id;
    $image->save();

    Message::create([
      'user_id'=>Auth::user()->id,
      'subject'=>"Image review in progress",
      'body'=>View::make('messages.image.in_review')->with(['image'=>$image]),
    ]);
    return Redirect::to($candidate->canonical_url);
  }
  if($state=='done')
  {
    $with['candidate'] = $candidate;
  }
  return View::make('contests.join')->with($with);
}]);



//Route::get('/live/', ['as'=>'contest.live.view', 'uses'=>'LiveController@view']); 

//Route::get('/join/{id}/done', ['before'=>'auth', 'as'=>'contests.candidates.after_join', 'uses'=>'JoinController@done']);
Route::get('/join/{id}/done', ['before'=>'auth', 'as'=>'contests.candidates.after_join', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  if($contest->is_ended)
  {
    Session::put('danger', "Sorry, voting has ended.");
    return Redirect::to($contest->canonical_url);
  }  
  return View::make('contests.after_join')->with(['candidate'=>$candidate, 'contest'=>$contest]);
}]);


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

Route::get('/live', ['as'=>'contests.live', 'uses'=>'HomeController@live']);
Route::get('/archived', ['as'=>'contests.archived', 'uses'=>'HomeController@archived']);

Route::get('/leaderboard', ['as'=>'leaderboard', 'uses'=>'LeaderboardController@index']);

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

Route::get('/advertise', ['as'=>'privacy', 'uses'=>function() {
  return View::make('legal.advertise');
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


Route::get('/usercontext', ['as'=>'usercontext', 'uses'=>'UserContextController@go']);

Route::get('/calcstats', ['as' => 'cal_stats', 'uses'=>'VoteController@calcstats']);

// Admin Routes

Route::group(array('prefix'=> 'admin', 'before' => ['auth.admin'],['forceHttps']), function() {

    Route::get('/', array('uses' => 'Admin\\DashboardController@index', 'as' => 'admin.home'));

    Route::get('images', ['as'=>'admin.images', 'uses'=>'Admin\\ImageController@index']);
    Route::get('images/{image_id}/{status}', ['as'=>'admin.images.status', 'uses'=>'Admin\\ImageController@set_status']);

    Route::get('badges', ['as'=>'admin.badges', 'uses'=>'Admin\\BadgeController@index']);

    Route::get('contests/add', 'Admin\\ContestController@add');
    
    // Resource Controller for user management, nested so it needs to be relative
    Route::resource('users', 'Admin\\UserController');

    Route::resource('contests', 'Admin\\ContestController');
    Route::resource('contests/{id}/edit/', 'Admin\\ContestController@edit');
    Route::resource('contests/add', 'Admin\\ContestController@add');


    Route::resource('candidates', 'Admin\\CandidateController');
    Route::resource('candidates/{id}/edit/', 'Admin\\CandidateController@edit');

    Route::resource('sponsors', 'Admin\\SponsorController');
    Route::resource('sponsors/{id}/edit/', 'Admin\\SponsorController@edit');

});

