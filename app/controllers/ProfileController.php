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
    
    $open_candidates = Candidate::open_active_candidates()
      ->join('votes', 'votes.candidate_id', '=', 'candidates.id')
      ->where('votes.user_id', '=', $u->id)
      ->with(['contest', 'image'])
      ->get();

    $closed_candidates = Candidate::closed_active_candidates()
      ->join('votes', 'votes.candidate_id', '=', 'candidates.id')
      ->where('votes.user_id', '=', $u->id)
      ->with(['contest', 'image'])
      ->get();
     
    $current_contests =  DB::table('candidates')
            ->join('contests', 'contests.id', '=', 'candidates.contest_id')
            ->where('candidates.user_id', '=', $u->id)
            ->where('contests.ends_at', '>=', new DateTime('today'))
            ->select('candidates.id', 'contests.title', 'contests.id as contest_id')
            ->get();

    $past_contests = DB::table('candidates')
            ->join('contests', 'contests.id', '=', 'candidates.contest_id')
            ->where('candidates.user_id', '=', $u->id)
            ->where('contests.ends_at', '<', new DateTime('today'))
            ->select('candidates.id', 'contests.title', 'contests.id as contest_id')
            ->get();
    
    return View::make('profile.view', [
      'open_candidates'=>$open_candidates, 
      'closed_candidates'=>$closed_candidates,
      'current_contests'=>$current_contests,
      'past_contests'=>$past_contests, 
      'user'=>$u, 
      'is_self'=>Auth::user() ? $u->id==Auth::user()->id : false
    ]);
    
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
