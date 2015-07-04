<?php

class LiveController extends \BaseController {
  function live($view_mode='large') {
    $contest_id = 33;
    $contest = Contest::find($contest_id);
    //print_r($contest);exit;
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
    return View::make('contests.view.'.$view_mode)->with(['contest'=>$contest, 'view_mode'=>'large']);
  }

}
