<?php
  
class User extends Eloquent
{
  function contests()
  {
    return $this->hasMany('Contest');
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
  
  function to_json()
  {
    return 
      [
        'fb_id'=>$this->fb_id,
        'first_name'=>$this->first_name,
        'last_name'=>$this->last_name,
        'email'=>$this->email,
        'is_contributor'=>$this->is_contributor,
      ];
  }
}