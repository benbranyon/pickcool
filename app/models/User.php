<?php

use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\Mutex;

class User extends Eloquent
{
  use RawDb;
  
  function log_activity()
  {
    $log = new ActivityLog();
    $log->save();
  }
  
  function getProfileImageUrlAttribute()
  {
    return "//graph.facebook.com/v2.2/{$this->fb_id}/picture?type=square&width=1500&height=1500";
  }


  
  
  
  static function calc_points()
  {
    self::calc_earned();
    self::calc_pending();
  }
  
  static function calc_earned()
  {
    $sqls = [];
    $sqls[] = "
      update users u 
        join (
          select 
            u.id, 
            sum(v.votes_ahead) as earned_points,
            max(v.voted_at) as most_recent_voted_at
          from 
            users u 
              join 
            votes v 
              on u.id = v.user_id 
              join
            contests c
              on 
            c.id=v.contest_id
              and
            c.ends_at < utc_timestamp()
          group by 
            u.id
        ) d
          on 
        u.id = d.id
  	set 
      u.earned_points = d.earned_points, 
      u.most_recent_voted_at = d.most_recent_voted_at
    ";
    self::execute($sqls);

  }
  
  static function calc_pending()
  {
    $sqls = [];
    $sqls[] = "
      update users u 
        join (
          select 
            u.id, 
            sum(v.votes_ahead) as pending_points
          from 
            users u 
              join 
            votes v 
              on u.id = v.user_id 
              join
            contests c
              on 
            c.id=v.contest_id
              and
            c.ends_at > utc_timestamp()
          group by 
            u.id
        ) d
          on 
        u.id = d.id
  	set 
      u.pending_points = d.pending_points
    ";    
    self::execute($sqls);
  }
  
  static function calc_ranks()
  {
    
    $sqls = [];
    $sqls[] = "
      set @r:=0;
    ";
    $sqls[] = "
      update users u join (
      select 
      	id,
      	earned_points, 
      	most_recent_voted_at, 
      	case 
      		when is_visible then
      			@r:=@r+1
      		when is_visible = 0 then
      			0 
      	end as rank 
      from 
      	users 
      order by 
      	earned_points desc, 
      	most_recent_voted_at asc, 
      	users.id asc
      ) d
      on u.id = d.id
      set u.rank = d.rank
    ";
    self::execute($sqls);
  }
  
  function getProfileUrlAttribute()
  {
    return route('profile', [$this->id]);
  }
  
  static function calc_stats()
  {
    self::calc_points();
    self::calc_ranks();
  }
  
  function pending_points_for($c)
  {
    if($this->is_in_contest($c)) return 0;
    $v = $this->votes()->whereContestId($c->id)->whereUserId($this->id)->first();
    if(!$v) return 0;
    return $v->votes_ahead;
  }
  
  function is_in_contest($c)
  {
    return $c->candidates()->where('user_id', '=', $this->id)->count()>0;
  }
  

  function votes()
  {
    return $this->hasMany('Vote');
  }
  
  function getFullNameAttribute()
  {
    return "{$this->first_name} {$this->last_name}";
  }
  
  function getHashTagAttribute()
  {
    return preg_replace("/[^A-Za-z0-9]/", "", ucwords($this->full_name));
  }
  
  function contests()
  {
    return $this->hasMany('Contest');
  }
  
  function current_vote_for($contest)
  {
    return Vote::whereUserId($this->id)->whereContestId($contest->id)->first();
  }
  
  function profile_image_url($cache_bust = false)
  {
    $extra = $cache_bust ? '&_r='.time() : '';
    return "https://graph.facebook.com/{$this->fb_id}/picture?width=1200&height=1200".$extra;
  }
  
  function messages()
  {
    return $this->hasMany('Message')->orderBy('created_at', 'desc');
  }
  
  function getHasUnreadMessagesAttribute()
  {
    return $this->messages()->whereNull('read_at')->count() > 0;
  }
  
  function getHasMessagesAttribute()
  {
    return $this->messages()->count() > 0;
  }
  
  function getHasReadMessagesAttribute()
  {
    return $this->messages()->whereNotNull('read_at')->count() > 0;
    
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
      $optional_fields = [
        'first_name', 'last_name', 'email', 'gender',
      ];
      foreach($optional_fields as $field_name)
      {
        if(!isset($me[$field_name]) || !trim($me[$field_name])) continue;
        $user->$field_name = $me[$field_name];
      }
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
      $result = 'new';
      $v = new Vote();
      $v->user_id = $this->id;
      $v->contest_id = $c->contest_id;
      $v->candidate_id = $c->id;
    } else {
      $v->candidate_id = $c->id;
      if($v->isDirty())
      {
        $result = 'changed';
      } else {
        $result = 'unchanged';
      }
    }
    $v->votes_ahead = 0;
    $v->voted_at = Carbon::now();
    $v->save();
    return [$result, $v];
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