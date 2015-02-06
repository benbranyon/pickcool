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
  $contests = Contest::hot();
  //Session::flush();
  return View::make('home')->with(['contests'=>$contests]);
}]);

Route::get('/est/{contest_id}/{slug}/{view_mode?}', ['as'=>'contest.view', 'uses'=>function($contest_id, $slug, $view_mode=null) {
  $contest = Contest::find($contest_id);
  if(!$contest)
  {
    App::abort(404);
  }
  if(!$contest->can_view)
  {
    return Redirect::to($contest->login_url);
  }
  if(!$view_mode)
  {
    $view_mode = session_get('contest_view_mode', ['small', 'large', 'realtime']);
  }
  Session::put('contest_view_mode', $view_mode);
  return View::make('contests.view.'.$view_mode)->with(['contest'=>$contest, 'view_mode'=>$view_mode]);
}]);

Route::get('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}', ['as'=>'contests.candidate.view', 'uses'=>function($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
  $contest = Contest::find($contest_id);
  if(!$contest)
  {
    App::abort(404);
  }
  if(!$contest->can_view)
  {
    Session::put('r', Request::url());
    return Redirect::to($contest->login_url);
  }
  $candidate = Candidate::find($candidate_id);
  if(!$candidate || !$candidate->is_active)
  {
    App::abort(404);
  }
  if(Auth::user() && Input::get('v'))
  {
    Auth::user()->vote_for($candidate);
  }
  return View::make('contests.candidates.view')->with(['contest'=>$contest, 'candidate'=>$candidate]);
}]);

Route::get('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}/manage/{cmd}/{cmd_id}', ['before'=>'auth', 'as'=>'contests.candidate.manage', 'uses'=>function($contest_id, $contest_slug, $candidate_id, $candidate_slug, $cmd_name, $cmd_id) {
  $contest = Contest::find($contest_id);
  if(!$contest)
  {
    App::abort(404);
  }
  if(!$contest->can_view)
  {
    Session::put('r', Request::url());
    return Redirect::to($contest->login_url);
  }
  $candidate = Candidate::find($candidate_id);
  if(!$candidate)
  {
    App::abort(404);
  }
  if(Auth::user() && Input::get('v'))
  {
    Auth::user()->vote_for($candidate);
  }
  if(!$candidate->is_active)
  {
    App::abort(404);
  }
  if($candidate->user_id != Auth::user()->id)
  {
    Session::put('danger', "You are not authorized to manage this candidate.");
    return Redirect::to($candidate->canonical_url);
  }
  $image = Image::find($cmd_id);
  if(!$image || !$image->candidate_id == $candidate->id)
  {
    Session::put('danger', "You are not authorized to manage this candidate.");
    return Redirect::to($candidate->canonical_url);
  }
  if($image->id == $candidate->image_id)
  {
    Session::put('danger', "You cannot manage the featured picture. Change the featured picture, then manage it.");
    return Redirect::to($candidate->canonical_url);
  }
  
  switch($cmd_name)
  {
    case 'featured':
      if($image->status != 'featured')
      {
        Session::put('danger', "You cannot feature this image.");
        return Redirect::to($candidate->canonical_url);
      }
      Session::put('success', 'Featured image updated.');
      $candidate->image_id = $image->id;
      $candidate->save();
      break;
    case 'delete':
      Session::put('success', 'Image deleted.');
      $image->delete();
      break;
    case 'moveup':
      $images = $candidate->weighted_images;
      $weight = 0;
      for($i=0;$i<count($images);$i++)
      {
        $images[$i]->weight = $weight++;
        if($images[$i]->id == $image->id)
        {
          if($i>0) $images[$i-1]->weight++;
          $images[$i]->weight--;
        }
      }
      foreach($images as $i) $i->save();
      break;
    case 'movedown':
      $images = $candidate->weighted_images;
      $weight = 0;
      for($i=0;$i<count($images);$i++)
      {
        $images[$i]->weight = $weight++;
        if($i>0 && $images[$i-1]->id == $image->id)
        {
          $images[$i-1]->weight++;
          $images[$i]->weight--;
        }
      }
      foreach($images as $i) $i->save();
      break;
      
  }
  return Redirect::to($candidate->canonical_url);
}]);

Route::any('/est/{contest_id}/{slug}/login', ['before'=>['auth'], 'as'=>'contest.login', 'uses'=>function($contest_id) {
  $contest = Contest::find($contest_id);
  if(!$contest)
  {
    App::abort(404);
  }
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


Route::any('/join/{id}', ['before'=>'auth', 'as'=>'contest.join', 'uses'=>function($id) {
  $contest = Contest::find($id);
  if(!$contest)
  {
    App::abort(404);
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


Route::any('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}/images', ['as'=>'contests.candidates.images', 
'uses'=>function($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
  $contest = Contest::find($contest_id);
  $candidate = Candidate::find($candidate_id);
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  
  return View::make('contests.candidates.images.list')->with(['contest'=>$contest, 'candidate'=>$candidate]);
}]);

Route::any('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}/images/add', ['as'=>'contests.candidates.images.add', 
'uses'=>function($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
  $contest = Contest::find($contest_id);
  $candidate = Candidate::find($candidate_id);
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  
  if(Request::isMethod('post'))
  {
    $file = Input::file('picture');
    if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
    {
      Session::put('danger', 'Please upload an image file before continuing.');
    } else {
      try
      {
        $image = new Image();
        $image->image = $file;
        $image->candidate_id = $candidate->id;
        $image->save();
        Session::put('success', 'Your image has been sumbitted and will be reviewed in the next 24-48 hours.');
        return Redirect::to($candidate->canonical_url);
      } catch (Exception $e) {
        Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
      }
    }
  }  
  return View::make('contests.candidates.images.add')->with(['contest'=>$contest, 'candidate'=>$candidate]);
}]);

Route::get('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}/refresh', ['as'=>'contests.candidate.refresh', 'uses'=>function($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
  $contest = Contest::find($contest_id);
  $candidate = Candidate::find($candidate_id);
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  $contest->add_user();
  Session::put('success', "Your information has been refreshed.");
  return Redirect::to($candidate->canonical_url);
}]);


Route::get('/join/{id}/done', ['before'=>'auth', 'as'=>'contests.candidates.after_join', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  return View::make('contests.after_join')->with(['candidate'=>$candidate, 'contest'=>$contest]);
}]);


Route::get('/login', ['as'=>'login', 'uses'=>function() {
  Session::put('onsuccess', Input::get('success', Session::get('onsuccess', Request::url())));
  Session::put('oncancel', Input::get('cancel', Session::get('oncancel', Request::url())));
  return View::make('login');
}]);
Route::get('/logout', ['as'=>'logout', 'uses'=>function() {
  Session::flush();
  Session::put('success', 'You have been logged out.');
  return Redirect::to(r('home'));
}]);


Route::get('/facebook/authorize', ['as'=>'facebook.authorize', 'uses'=>function() {
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
}]);

Route::get('/facebook/authorize/retry', ['as'=>'facebook.authorize.retry', 'uses'=>function() {
  return View::make('facebook.authorize.retry');
}]);

Route::get('/vote/{id}', ['before'=>'auth', 'as'=>'candidates.vote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
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
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  return View::make('contests.candidates.after_vote')->with(['candidate'=>$candidate, 'contest'=>$contest]);
}]);


Route::get('/unvote/{id}', ['before'=>'auth', 'as'=>'candidates.unvote', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  Auth::user()->unvote_for($candidate);
  Session::put('success', "Ok, you unvoted {$candidate->name}");
  return Redirect::to($candidate->canonical_url);
}]);

Route::get('/candidate/charity_boost/{id}', ['as'=>'candidate.charity_boost', 'uses'=>function($id) {
  $candidate = Candidate::find($id);
  $contest = $candidate->contest;
  if(!$contest || !$candidate)
  {
    App::abort(404);
  }
  $badge = new Badge();
  $badge->name = 'charity';
  $badge->vote_weight = 25;
  $badge->contest_id = $contest->id;
  $badge->candidate_id = $candidate->id;
  $badge->save();  
  Session::put('success', "Ok, {$candidate->name} now has a charity badge");
  return \Redirect::to('admin/candidates');
}]);

Route::get('/sponsor/signup/{id}', ['before'=>'auth', 'as'=>'sponsors.signup', 'uses'=>function($id) {
  $contest = Contest::find($id);
  if(!$contest)
  {
    App::abort(404);
  }
  $fb = \OAuth::consumer( 'Facebook' );
  //$has_token = $fb->getStorage()->hasAccessToken("Facebook");

  try
  {
    $fb_token = $fb->getStorage()->retrieveAccessToken("Facebook"); 
    $access_token = $fb_token->getAccessToken();
    $client = new \GuzzleHttp\Client();
    $fb_permissions =  $client->get('https://graph.facebook.com/me/permissions?access_token=' . $access_token); 
    $fb_permissions = $fb_permissions->json();
  }
  catch (Exception $e) {
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

Route::get('/inbox/{message_id}/read', ['before'=>'auth', 'as'=>'inbox.read', 'users'=>function($message_id) {
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
