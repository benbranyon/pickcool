<?php

class User extends Eloquent
{
  function contests()
  {
    return $this->hasMany('Contest');
  }
  
  function current_vote_for($contest)
  {
    return Vote::whereUserId(Auth::user()->id)->whereContestId($contest->id)->first();
  }
  
  function profile_image_url()
  {
    return "https://graph.facebook.com/{$this->fb_id}/picture?width=1200&height=1200";
  }
  
  static function from_fb($me)
  {
    $fb_id = $me->getId();
    $user = User::whereFbId($fb_id)->first();
    if(!$user)
    {
      $user = new User();
    }
    $user->fb_id = $fb_id;
    $user->first_name = $me->getFirstName();
    $user->last_name = $me->getLastName();
    $user->email = $me->getEmail();
    $user->gender = $me->getProperty('gender');
    $user->save();
    
    return $user;
  }
  
  function vote_for($c)
  {
    if(is_numeric($c))
    {
      $c = Candidate::find($c);
    }
    if(!$c) return;
    $v = Vote::whereUserId($this->id)->whereContestId($c->contest_id)->first();
    if(!$v)
    {
      $v = new Vote();
      $v->user_id = $this->id;
      $v->contest_id = $c->contest_id;
    }
    $v->candidate_id = $c->id;
    $v->save();
  }
  
  function unvote_for($c)
  {
    if(is_numeric($c))
    {
      $c = Candidate::find($c);
    }
    if(!$c) return;
    $v = Vote::whereUserId($this->id)->whereContestId($c->contest_id)->first();
    if(!$v) return;
    $v->delete();
  }  
}