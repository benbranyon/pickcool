<?php

class ContestViewController extends BaseController
{
  function view($contest_id, $contest_slug, $candidate_id, $candidate_slug)
  {
    return $this->process_hit($contest_id, $candidate_id);
  }
  

  function view_old($contest_id, $slug, $user_id=null, $candidate_id=null)
  {
    return $this->process_hit($contest_id, $candidate_id, $user_id);
  }
  
  function process_hit($contest_id, $candidate_id=null, $user_id=null)
  {
    $is_facebook = preg_match("/facebookexternalhit/", Request::server('HTTP_USER_AGENT')) || Input::get('f');
    if(!$is_facebook) return View::make('app');

    $c = Contest::find($contest_id);
    if($candidate_id)
    {
      $w = Candidate::find($candidate_id);
      $data = [
        'title'=>"Vote {$w->name} in {$c->title}",
        'canonical_url'=>route('contest.candidate.view', [$c->id, $c->slug(), $w->id, $w->slug()]),
        'image_url'=>route('image.view', [$w->image_id, 'facebook']),
        'description'=>"Cast your vote and watch the contest at pick.cool.",
      ];
    } else {
      $w = $c->current_winner();
      $data = [
        'title'=>"Vote in {$c->title}",
        'canonical_url'=>route('contest.view', [$c->id, $c->slug()]),
        'image_url'=>route('image.view', [$w->image_id, 'facebook']),
        'description'=>"{$w->name} (above) leads. Cast your vote and watch the contest at pick.cool.",
      ];
    }
    return View::make('contest.spider')->with($data);
  }
}