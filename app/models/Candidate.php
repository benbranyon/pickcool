<?php
use Cocur\Slugify\Slugify;
  
class Candidate extends Eloquent
{
  function getAfterJoinUrlAttribute()
  {
    return route("candidates.after_join", [$this->id]);
  }

  function getAfterVoteUrlAttribute()
  {
    return route('candidates.after_vote', [$this->id]);
  }
  
  function getLoginUrlAttribute()
  {
    return route('facebook.authorize', ['success'=>$this->canonical_url, 'cancel'=>$this->canonical_url]);
  }
  
  function getIsWriteinAttribute()
  {
    return $this->is_writein();
  }
  function is_writein($user=null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return false;
    return $this->fb_id == $user->fb_id;
  }
  
  function getIsVoteableAttribute()
  {
    return $this->contest->is_voteable;
  }
  
  function getIsUserVoteAttribute()
  {
    $user = Auth::user();
    if(!$user) return false;
    return Vote::whereUserId($user->id)->whereCandidateId($this->id)->first()!=null;
  }
  
  function getVoteCountAttribute()
  {
    return $this->total_votes;
  }

  function getVoteUrlAttribute()
  {
    return route('candidates.vote', [$this->id]);
  }
  
  function getUnvoteUrlAttribute()
  {
    return route('candidates.unvote', [$this->id]);
  }
  
  function getCanonicalUrlAttribute()
  {
    return route('contest.candidate.view', [$this->contest->id, $this->contest->slug(), $this->id, $this->slug]);
  }
  
  function getUnfollowUrlAttribute()
  {
    return route('contest.candidate.unfollow', [$this->id]);
  }
  
  function votes_ago($ago='0 day')
  {
    return $this->votes()->where(function($query) use($ago) {
  //    $query->whereRaw('created_at < utc_timestamp() - interval '.$ago);
    });
  }
  
  function getEarliestVoteAttribute()
  {
    $vote = $this->votes()->orderBy('created_at')->first();
    return $vote;
  }
  
  function getUserAttribute()
  {
    if(!$this->fb_id) return null;
    $u = User::whereFbId($this->fb_id)->first();
    return $u;
  }
  
  function getSlugAttribute()
  {
    $slugify = new Slugify();
    return $slugify->slugify($this->name, '_');
  }

  function getCanVoteAttribute()
  {
    return $this->contest->can_vote;
  }


  function getImageUrlAttribute($size='thumb')
  {
    return route('image.view', [$this->image->id, $size]);
  }
  
  function image()
  {
    return $this->belongsTo('Image');
  }
  
  function contest()
  {
    return $this->belongsTo('Contest');
  }

  public function votes()
  {
    return $this->hasMany('Vote');
  }
  
  function is_editable_by($user)
  {
    return $this->contest->is_editable_by($user);
  }
  
  function is_moderator($user)
  {
    return $this->contest->is_moderator($user);
  }
  
  protected static function boot() {
    parent::boot();
    static::deleting(function($candidate) { // called BEFORE delete()
      $candidate->votes()->delete();
    });
  }
}


Candidate::created(function($obj) {
  $obj->contest->calc_ranks('0 day', true);
});

