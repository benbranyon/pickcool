<?php

class LeaderboardController extends \BaseController {

  public function index()
  {
    $users = User::query()->whereIsVisible(1)->orderBy('rank')->simplePaginate(20);
    return View::make('leaderboard.index', ['users'=>$users]);
    
  }


}
