<?php

class ContestController extends \BaseController {

  function view($contest_id, $slug, $view_mode=null) 
  {
    $contest = Contest::with('category', 'sponsors')->whereId($contest_id)->first();
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
  }	
  


  function login($contest_id) {
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
  }
  

  

}
