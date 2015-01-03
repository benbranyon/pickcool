<?php

use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Mutex;

class User extends Eloquent
{
  function getFullNameAttribute()
  {
    return "{$this->first_name} {$this->last_name}";
  }
  
  function toHashTag()
  {
    return preg_replace("/[^A-Za-z0-9]/", "", ucwords($this->full_name()));
  }
  
  function contests()
  {
    return $this->hasMany('Contest');
  }
  
  function current_vote_for($contest)
  {
    return Vote::whereUserId(Auth::user()->id)->whereContestId($contest->id)->first();
  }
  
  function profile_image_url($cache_bust = false)
  {
    $extra = $cache_bust ? '&_r='.time() : '';
    return "https://graph.facebook.com/{$this->fb_id}/picture?width=1200&height=1200".$extra;
  }
  
  static function from_fb($me)
  {
    $fb_id = $me['id'];
    $user = Lock::go('create_'.$fb_id, function() use ($fb_id, $me) {
      $user = User::whereFbId($fb_id)->first();
      if(!$user)
      {
        $user = new User();
      }
      $user->fb_id = $fb_id;
      $user->first_name = $me['first_name'];
      $user->last_name = $me['last_name'];
      $user->email = $me['email'];
      $user->gender = $me['gender'];
      $user->save();
      return $user;
    }, $fb_id, $me);
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