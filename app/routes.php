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
      if($class=='Contest')
      {
        $v = null;
        if(Auth::check())
        {
          $v = Auth::user()->current_vote_for($obj);
        }
        $contest = [
          'id'=>$obj->id,
          'max_votes'=>10,
          'current_user_candidate_id'=>$v ? $v->candidate_id : null,
          'canonical_url'=>route('contest.view', [$obj->id, $obj->slug()]),
          'candidates'=>[],
        ];
        foreach($obj->candidates as $can)
        {
          $contest['candidates'][] = [
            'name'=>$can->name,
            'image_url'=>$can->image->image->url($size),
            'vote_count'=>$can->votes()->count(),
            'vote_pct'=>max(1,$can->votes->count()/floatval(10)),
            'id'=>$can->id,
          ];
        }      
        return $contest;
      }
    
      if($class=='User')
      {
        return [
          'fb_id'=>$obj->fb_id,
          'first_name'=>$obj->first_name,
          'last_name'=>$obj->last_name,
          'email'=>$obj->email,
          'is_contributor'=>$obj->is_contributor,
        ];
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
  Route::any('/my/contests/create', function() {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }

    $has_error = false;
    $data = json_decode(Input::get('contest'),true);
    array_walk_recursive($data, function($v,$k) {
      $v = trim($v);
    });
    
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
      $data['title']['errors'] = $validator->messages()->get('title.value');
    }
    
    foreach($data['candidates'] as &$rec)
    {
      $rec['errors'] = [];
      if(!isset($rec['name']) || !$rec['name']) continue;
      $validator = Validator::make(
        $rec,
        [
          'name.value'=>'required',
          'image_url.value'=>'required',
          'amazon_url.value'=>'required',
        ],
        [
          'name.value.required' => 'Candidate name is required',
          'image_url.value.required' => 'Candidate image URL is requred',
          'amazon_url.value.required' => 'Candidate store URL is requred',
        ]
      );
      if($validator->fails())
      {
        $has_error = true;
        $m = $validator->messages();
        if($m->get('name.value')) $rec['name']['errors'] = $m->get('name.value');
        if($m->get('image_url.value')) $rec['image_url']['errors'] = $m->get('image_url.value');
        if($m->get('amazon_url.value')) $rec['amazon_url']['errors'] = $m->get('amazon_url.value');
      }
    }
    if( $has_error)
    {
      return ApiSerializer::error(API_ERR_VALIDATION, $data);
    }
    $contest = new Contest();
    $contest->title = $data['title']['value'];
    $contest->user_id = Auth::user()->id;
    $contest->save();
    foreach($data['candidates'] as $rec)
    {
      if(!isset($rec['name']) || !$rec['name']) continue;
      $i = new Image();
      $i->image = $rec['image_url']['value'];
      $i->save();
      $c = new Candidate();
      $c->contest_id = $contest->id;
      $c->name = $rec['name']['value'];
      $c->image_id = $i->id;
      $c->amazon_url = $rec['amazon_url']['value'];
      $c->save();
    }
    return ApiSerializer::ok();
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
  
  Route::any('/contests/featured', function() {
    $contests = [];
    foreach(Contest::all() as $c)
    {
      $contests[] = ApiSerializer::serialize($c,'tiny');
    }
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

Route::get('/', function() {
  return 'Coming Soon';
});

Route::get('/beta', function() {
  return View::make('app');
});

Route::get('/est/{id}/{slug}', ['as'=>'contest.view', function($id, $slug) {
  return 'hello world';
}]);

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