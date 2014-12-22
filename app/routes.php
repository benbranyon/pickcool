<?php

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
  Route::any('/contests/{id}', 'Api\V1\ContestController@get');

  // Wildcard if no matched route
  Route::any( '/', function(  ){
    return ApiSerializer::ok();
  })->where('all', '.*');
  Route::any( '{all}', function( $uri ){
    return ApiSerializer::ok();
  })->where('all', '.*');
});

Route::get('/est/{contest_id}/{contest_slug}/picks/{candidate_id}/{candidate_slug}', ['as'=>'contest.candidate.view', 'uses'=>'ContestViewController@view']);
Route::get('/est/{contest_id}/{slug}/{user_id?}/{candidate_id?}', 'ContestViewController@view_old');

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
