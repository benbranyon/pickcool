<?php

class VoteController extends \BaseController {
  function vote($id) {
    $candidate = Candidate::find($id);
    $contest = $candidate->contest;
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    if($contest->is_ended)
    {
      Session::put('danger', "Sorry, voting has ended.");
      return Redirect::to($contest->canonical_url);
    }

    list($result,$v) = Auth::user()->vote_for($candidate);
    $qs = [
      'v'=>$result,
    ];
    $qs = http_build_query($qs);

    //Update user pending score
    User::calc_pending();
    return Redirect::to($candidate->after_vote_url."?{$qs}");
  }
  
  function done($id) {
    $candidate = Candidate::find($id);
    $contest = $candidate->contest;
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    return View::make('contests.candidates.after_vote')->with(['candidate'=>$candidate, 'contest'=>$contest]);
  }  

  function unvote($id) {
    $candidate = Candidate::find($id);
    $contest = $candidate->contest;
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    if($contest->is_ended)
    {
      Session::put('danger', "Sorry, voting has ended.");
      return Redirect::to($contest->canonical_url);
    }
    Auth::user()->unvote_for($candidate);
    Session::put('success', "Ok, you unvoted {$candidate->name}");
    return Redirect::to($candidate->canonical_url);
  }

  function calcstats() {
    Contest::calc_stats();
    //Vote::calc_votes_ahead();
    User::calc_pending();
    Flatten::flushRoute('contests.live');
    Flatten::flushRoute('contests.archived');
    //Contest::calc_stats();
    print_r('done');exit;
  }
}
