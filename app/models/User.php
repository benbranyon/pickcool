<?php
  
class User extends EloquentBase
{
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
    $v = Vote::whereUserId($this->id)->whereCandidateId($c->id)->first();
    if(!$v)
    {
      $v = new Vote();
      $v->user_id = $this->id;
      $v->candidate_id = $c->id;
    }
    $v->save();
  }
}