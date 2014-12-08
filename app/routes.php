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



Route::group([
  'prefix' => 'api/v1',
  'before'=>'origin',
], function() {
  Route::any('/my/contests', function() {
    if(!Auth::user())
    {
      return json_encode(['status'=> 'error', 'error_code'=>1, 'error_message'=>'Authentication is required for this operation.']);
    }
    $contests = [];
    foreach(Auth::user()->contests()->get() as $c)
    {
      $contest = [
        'id'=>$c->id,
        'candidates'=>[],
      ];
      foreach($c->candidates as $can)
      {
        $contest['candidates'][] = [
          'name'=>$can->name,
          'image_url'=>$can->image->image->url('admin'),
        ];
      }
      $contests[] = $contest;
    }
    return json_encode([
      'status'=>'ok',
      'data'=>$contests,
    ]);
  });
  Route::any('/my/contests/create', function() {
    if(!Auth::user())
    {
      return json_encode(['status'=> 'error', 'error_code'=>1, 'error_message'=>'Authentication is required for this operation.']);
    }
    
    $data = json_decode(Input::get('candidates'),true);
    array_walk_recursive($data, function($v,$k) {
      $v = trim($v);
    });
    $errors = [];
    foreach($data as $rec)
    {
      if(!isset($rec['name']) || !$rec['name']) continue;
      $validator = Validator::make(
          $rec,
          [
            'name'=>'required',
            'image_url'=>'required',
            'amazon_url'=>'required',
          ]
      );
      if($validator->fails())
      {
        $errors[] = [
          'id'=>$rec['id'],
          'messages'=>$validator->messages()->toArray(),
        ];
      }
    }
    if($errors)
    {
      return json_encode([
        'status'=>'error',
        'error_code'=>2,
        'error_message'=>$errors,
      ]);
    }
    $contest = new Contest();
    $contest->user_id = Auth::user()->id;
    $contest->save();
    foreach($data as $rec)
    {
      if(!$rec['name']) continue;
      $i = new Image();
      $i->image = $rec['image_url'];
      $i->save();
      $c = new Candidate();
      $c->contest_id = $contest->id;
      $c->name = $rec['name'];
      $c->image_id = $i->id;
      $c->amazon_url = $rec['amazon_url'];
      $c->save();
    }
    return json_encode([
      'status'=>'ok',
    ]);
    
  });
  Route::any('/user', function() {
    if(!Auth::user())
    {
      return json_encode(['status'=> 'error', 'error_code'=>1, 'error_message'=>'Authentication is required for this operation.']);
    }
    return json_encode([
      'status'=>'ok',
      'data'=>Auth::user()->to_json()
    ]);
    
  });
  
  Route::any('/vote', function() {
    if(!Auth::user())
    {
      return json_encode(['status'=> 'error', 'error_code'=>1, 'error_message'=>'Authentication is required for this operation.']);
    }
    Auth::user()->vote_for(Input::get('c'));
    return json_encode(['status'=>'ok']);
  });
  
  Route::any('/contests/featured', function() {
    $contests = [];
    foreach(Contest::all() as $c)
    {
      $contest = [
        'id'=>$c->id,
        'candidates'=>[],
      ];
      foreach($c->candidates as $can)
      {
        $contest['candidates'][] = [
          'name'=>$can->name,
          'image_url'=>$can->image->image->url('tiny'),
          'vote_count'=>553,
          'id'=>$can->id,
        ];
      }
      $contests[] = $contest;
    }
    return json_encode([
      'status'=>'ok',
      'data'=>$contests,
    ]);
  });
  
  Route::any('/contests/{id}', function($id) {
    $c = Contest::find($id);
    $contest = [
      'id'=>$c->id,
      'candidates'=>[],
    ];
    foreach($c->candidates as $can)
    {
      $contest['candidates'][] = [
        'name'=>$can->name,
        'image_url'=>$can->image->image->url('thumb'),
        'vote_count'=>$can->votes()->count(),
        'id'=>$can->id,
      ];
    }
    return json_encode([
      'status'=>'ok',
      'data'=>$contest,
    ]);
  });

  Route::any( '/', function(  ){
    return json_encode(['status'=>'ok']);
  })->where('all', '.*');
  Route::any( '{all}', function( $uri ){
    return json_encode(['status'=>'ok']);
  })->where('all', '.*');
});

Route::get('/', function() {
  return 'Coming Soon';
});

Route::get('/beta', function() {
  return View::make('app');
});


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