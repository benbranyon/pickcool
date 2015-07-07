<?php

class ProfileController extends \BaseController {

  function home($id)
  {
    $u = User::find($id);
    if(!$u)
    {
      Session::put('danger', "That profile does not exist.");
      return Redirect::home();
    }
    
    return View::make('profile.view', ['user'=>$u, 'is_self'=>Auth::user() ? $u->id==Auth::user()->id : false]);
    
  }
  
  function settings()
  {
    if(!Auth::user()) return Redirect::to(r('login', ['success'=>Request::url()]));
    Auth::user()->is_visible = Input::get('is_visible',Auth::user()->is_visible)==true;
    Auth::user()->save();
    if(Auth::user()->is_visible) Session::put('success', "Your profile is now visible.");
    User::calc_ranks();
  }

  function set_visible()
  {
    if(!Auth::user()) return Redirect::to(r('login', ['success'=>Request::url()]));
    Auth::user()->is_visible = true;
    Auth::user()->save();
    User::calc_ranks();
    Session::put('success', "Congratulations, you're <b>in the game!</b> Now your profile is visible.");
    return Redirect::to(Input::get('r', route('home')));
    
  }
}
