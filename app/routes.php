<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

define('API_ERR_AUTH', 1);
define('API_ERR_VALIDATION', 2);
define('API_ERR_LOOKUP', 3);


function api_add_edit_contest()
{
  
  // If the user is not authenticated, bail
  if(!Auth::user())
  {
    return ApiSerializer::error(API_ERR_AUTH);
  }

  $has_error = false;
  $data = json_decode(Input::get('contest'),true);
  array_walk_recursive($data, function($v,$k) {
    $v = trim($v);
  });
  
  function init($rec, $name, $default=null) {
    return [
      'value'=>isset($rec[$name]['value']) ? $rec[$name]['value'] : $default,
      'errors'=>[],
    ];
  } 
  $res = [
    'id'=>init($data, 'id'),
    'title'=>init($data, 'title'),
  ];
  
  $validator = Validator::make(
    $data,
    [
      'title.value'=>'required',
    ],
    [
      'title.value.required'=>'Contest title is required',
    ]
  );
  if($validator->fails())
  {
    $has_error = true;
    $res['title']['errors'] = $validator->messages()->get('title.value');
  }
  
  // Validate candidates
  $res['candidates'] = [];
  foreach($data['candidates'] as $rec)
  {
    $candidate = [
      'id'=>init($rec, 'id'),
      'name'=>init($rec, 'name'),
      'image_url'=>init($rec, 'image_url'),
      'buy_url'=>init($rec, 'buy_url'),
      'buy_text'=>init($rec, 'buy_text'),
      'should_delete'=>init($rec, 'should_delete'),
    ];
    if(!$candidate['should_delete']['value'])
    {
      $validator = Validator::make(
        $rec,
        [
          'name.value'=>'required',
          'image_url.value'=>'required',
          'buy_url.value'=>'required',
          'buy_text.value'=>'required',
        ],
        [
          'name.value.required' => 'Candidate name is required',
          'image_url.value.required' => 'Candidate image URL is required',
          'buy_url.value.required' => 'Candidate buy URL is required',
          'buy_text.value.required' => 'Candidate buy text is required',
        ]
      );
      if($validator->fails())
      {
        $has_error = true;
        $m = $validator->messages();
        if($m->get('name.value')) $candidate['name']['errors'] = $m->get('name.value');
        if($m->get('image_url.value')) $candidate['image_url']['errors'] = $m->get('image_url.value');
        if($m->get('buy_url.value')) $candidate['buy_url']['errors'] = $m->get('buy_url.value');
        if($m->get('buy_text.value')) $candidate['buy_text']['errors'] = $m->get('buy_text.value');
      }
    }
    

    $res['candidates'][] = $candidate;
    
  }

  if( $has_error)
  {
    return ApiSerializer::error(API_ERR_VALIDATION, $res);
  }
  
  // Create or update
  // If an ID is provided, it means we're editing a contest
  // Make sure the user is allowed to edit
  if($res['id']['value'])
  {
    $contest = Contest::find($res['id']['value']);
    if(!$contest)
    {
      // If the record is not found, bail out
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    if(!$contest->is_editable_by(Auth::user()))
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
  } else {
    $contest = new Contest();
    $contest->user_id = Auth::user()->id; // Only assign user if creating
  }
  $contest->title = $res['title']['value'];
  $contest->save();
  $res['id']['value'] = $contest->id;
  // Update candidates

  foreach($res['candidates'] as $k=>$can)
  {
    if($can['id']['value'])
    {
      $c = Candidate::find($can['id']['value']);
      if(!$c)
      {
        return ApiSerializer::error(API_ERR_LOOKUP);
      }
      if(!$c->is_editable_by(Auth::user()))
      {
        return ApiSerializer::error(API_ERR_AUTH);
      }
      if($can['should_delete']['value'])
      {
        $c->delete();
        unset($can[$k]);
        continue;
      }
    } else {
      $c = new Candidate();
      $c->contest_id = $contest->id;
    }
    if($can['should_delete']['value']) continue;
    $i = Image::from_url($can['image_url']['value']);
    $c->name = $can['name']['value'];
    $c->image_id = $i->id;
    $c->buy_url = $can['buy_url']['value'];
    $c->buy_text = $can['buy_text']['value'];
    $c->save();
    $can['id']['value'] = $c->id;
  }
  return ApiSerializer::ok($contest);
}


class ApiSerializer
{
  static  $API_ERRORS=[
    API_ERR_AUTH=>'User not authorized to perform this operation.',
    API_ERR_VALIDATION=>'Data validation failed.',
    API_ERR_LOOKUP=>'Data not found',
  ];
  
  
  static function serialize($obj, $size='thumb')
  {
    if(is_object($obj))
    {
      $class = get_class($obj);
      Log::info($class);
      if($class=='Contest')
      {
        $v = null;
        if(Auth::check())
        {
          $v = Auth::user()->current_vote_for($obj);
        }
        $contest = [
          'id'=>$obj->id,
          'title'=>$obj->title,
          'owner_id'=>$obj->user_id,
          'current_user_candidate_id'=>$v ? $v->candidate_id : null,
          'is_editable'=>$obj->is_editable_by(Auth::user()),
          'slug'=>$obj->slug(),
          'candidates'=>$obj->candidates,
        ];
        return self::serialize($contest);
      }
      
      if($class=='Illuminate\Database\Eloquent\Collection')
      {
        $items = [];
        foreach($obj as $v) $items[] = $v;
        return self::serialize($items);
      }
      
      if($class=='Candidate')
      {
        $candidate = [
          'id'=>$obj->id,
          'name'=>$obj->name,
          'image_id'=>$obj->image_id,
          'vote_count'=>$obj->votes()->count(),
          'buy_text'=>$obj->buy_text
        ];
        if($obj->is_editable_by(Auth::user()))
        {
          $candidate['original_image_url'] = $obj->image ? $obj->image->url : null;
          $candidate['original_buy_url'] = $obj->buy_url;
        }
        
        return self::serialize($candidate);
        
      }
    
      if($class=='User')
      {
        return self::serialize([
          'id'=>$obj->id,
          'fb_id'=>$obj->fb_id,
          'first_name'=>$obj->first_name,
          'last_name'=>$obj->last_name,
          'email'=>$obj->email,
          'is_contributor'=>$obj->is_contributor,
        ]);
      }
          
      throw new Exception("API doesn't know how to serialize ".get_class($obj));
    }
    
    if(is_array($obj))
    {
      $new = [];
      foreach($obj as $k=>$v) $new[$k] = self::serialize($v,$size);
      $obj = $new;
    }
    
    return $obj;
  }
  
  static function error($errno, $data=null)
  {
    global $API_ERRORS;
    
    $payload = [
      'status'=> 'error', 
      'error_code'=>$errno,
      'error_message'=>self::$API_ERRORS[$errno],
      'data'=>self::serialize($data),
    ];
    return json_encode($payload);
  }
  
  static function ok($data=null)
  {
    $payload = [
      'status'=> 'ok', 
      'data'=>self::serialize($data),
    ];
    return json_encode($payload);
  }
}


Route::group([
  'prefix' => 'api/v1',
  'before'=>'origin',
], function() {
  Route::any('/my/contests', function() {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    $contests = [];
    foreach(Auth::user()->contests()->get() as $c)
    {
      $contests[] = ApiSerializer::serialize($c, 'admin');
    }
    return ApiSerializer::ok($contests);
  });
  Route::any('/contests/create', function() {
    return api_add_edit_contest();
  });
  
  Route::any('/contests/save', function() {
    return api_add_edit_contest();
  });
  
  Route::any('/user', function() {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    return ApiSerializer::ok(Auth::user());
    
  });
  
  Route::any('/vote', function() {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    
    $c = Candidate::find(Input::get('c'));
    if(!$c)
    {
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    Auth::user()->vote_for($c->id);
    return ApiSerializer::ok($c->contest);
  });
  
  Route::any('/contests/top', function() {
    $contests = Contest::join('votes', 'votes.contest_id', '=', 'contests.id', 'left outer')
      ->groupBy('contests.id')
      ->select(['contests.*', DB::raw('count(votes.id) as rank')])
      ->orderBy('rank', 'desc')
      ->get();
    return ApiSerializer::ok($contests);
  });

  Route::any('/contests/hot', function() {
    $contests = Contest::join('votes', 'votes.contest_id', '=', 'contests.id')
      ->whereRaw('votes.created_at > now() - interval 24 hour')
      ->groupBy('contests.id')
      ->select(['contests.*', DB::raw('count(votes.id) as rank')])
      ->orderBy('rank', 'desc')
      ->get();
    return ApiSerializer::ok($contests);
  });

  Route::any('/contests/new', function() {
    $contests = Contest::query()
      ->orderBy('created_at', 'desc')
      ->with('candidates', 'candidates.votes')
      ->get();
    return ApiSerializer::ok($contests);
  });
  
  Route::any('/contests/{id}', function($id) {
    $c = Contest::find($id);
    if(!$c)
    {
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    return ApiSerializer::ok(ApiSerializer::serialize($c, 'thumb'));
  });

  Route::any( '/', function(  ){
    return ApiSerializer::ok();
  })->where('all', '.*');
  Route::any( '{all}', function( $uri ){
    return ApiSerializer::ok();
  })->where('all', '.*');
});


Route::get('/est/{contest_id}/{slug}/{user_id?}/{candidate_id?}', ['as'=>'contest.view', function($contest_id, $slug, $user_id=null, $candidate_id=null) {
  $is_facebook = preg_match("/facebookexternalhit/", Request::server('HTTP_USER_AGENT')) || Input::get('f');
  if($is_facebook)
  {
    $picky_speak = [
      'Sad',
      'Goofy',
      'l33t',
      'pwnd',
      'Sketchy',
      'As if',
      'Solid',
      'Needs more cowbell',
    ];
    $picky = $picky_speak[rand(0,count($picky_speak)-1)];
    if($user_id)
    {
      $u = User::find($user_id);
      if($u)
      {
        $c = Contest::find($contest_id);
        $w = Candidate::find($candidate_id);
        $data = [
          'title'=>"{$u->first_name} voted {$w->name} coolest? \"{$picky},\" says Picky.",
          'canonical_url'=>route('contest.view', [$c->id, $c->slug(), $user_id, $candidate_id]),
          'image_url'=>route('image.view', [$w->image_id, 'facebook']),
          'description'=>"In recent news, {$u->first_name} cast a critical vote that {$w->name} really is cooler than {$c->candidateNamesForHumans($w->id, 'or')}. \"This is about to be a bad day if you're not a {$w->name} fan,\" said Picky McCool in an exclusive interview. \"But voting isn't over,\" he added. Vote now before it's too late!",
        ];
      }
    } else {
      $c = Contest::find($contest_id);
      $w = $c->current_winner();
      $data = [
        'title'=>"{$w->name} voted coolest? \"{$picky},\" says Picky.",
        'canonical_url'=>route('contest.view', [$c->id, $c->slug()]),
        'image_url'=>route('image.view', [$w->image_id, 'facebook']),
        'description'=>"Is {$w->name} really cooler than {$c->candidateNamesForHumans($w->id, 'or')}? \"This is about to be a bad day if you're not a {$w->name} fan,\" said Picky McCool in an exclusive interview. \"But voting isn't over,\" he added. Vote now before it's too late!",
      ];
      
    }
    return View::make('contest.spider')->with($data);
  }
  return View::make('app');
}]);

Route::get('/images/{id}/{size}', ['as'=>'image.view', function($id,$size) {
  $image = Image::find($id);
  if(!$image)
  {
    App::abort(404);
  }

  $response = Response::make(
     File::get($image->image->path($size)), 
     200
  );
  $response->header(
    'Content-type',
    'image/jpeg'
  );
  return $response;
}]);

Route::get('/shop/{candidate_id}', ['buy', function($candidate_id) {
  $candidate = Candidate::find($candidate_id);
  if(!$candidate)
  {
    App::abort(404);
  }
  
  return Redirect::to($candidate->buy_url);
}]);


Route::any('{url?}', function($url) { 
 return View::make('app');
})->where(['url' => '[-a-z0-9/]+']);


if (Config::get('database.log', false))
{    	 
  Event::listen('illuminate.query', function($query, $bindings, $time, $name)
  {
    $data = compact('bindings', 'time', 'name');

    // Format binding data for sql insertion
    foreach ($bindings as $i => $binding)
    {	 
      if ($binding instanceof \DateTime)
      {	 
        $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
      }
      else if (is_string($binding))
      {	 
        $bindings[$i] = "'$binding'";
      }	 
    }  	 

    // Insert bindings into query
    $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
    $query = vsprintf($query, $bindings); 

    Log::info($query, $data);
  });
}