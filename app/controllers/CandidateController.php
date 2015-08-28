<?php

class CandidateController extends \BaseController {
  function view($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
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
  }  
  
  function refresh($contest_id, $contest_slug, $candidate_id, $candidate_slug) 
  {
    $contest = Contest::find($contest_id);
    $candidate = Candidate::find($candidate_id);
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    $contest->add_user();
    Session::put('success', "Your information has been refreshed.");
    return Redirect::to($candidate->canonical_url);
  }
  
  function view_voters($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
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
    if(!$candidate->image_id)
    {
      return Redirect::to($candidate->canonical_url);
    }

    $votes = $candidate->votes()
      ->whereHas('user', function($q) { $q->where('is_visible', '=', 1); })
      ->with('user')
      ->orderBy('voted_at', 'desc')
      ->paginate(20);
    
    return View::make('contests.candidates.voters', ['contest'=>$contest, 'candidate'=>$candidate, 'votes'=>$votes]);
  }
}
