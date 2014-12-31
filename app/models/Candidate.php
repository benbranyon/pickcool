<?php
use Cocur\Slugify\Slugify;
  
class Candidate extends Eloquent
{
  
  function share_url()
  {
    return route('contest.candidate.view', [$this->contest->id, $this->contest->slug(), $this->id, $this->slug()]);
  }
  
  function unfollow_url()
  {
    return route('contest.candidate.unfollow', [$this->id]);
  }
  
  function votes_ago($ago='0 day')
  {
    return $this->votes()->where(function($query) use($ago) {
      $query->whereRaw('created_at < utc_timestamp() - interval '.$ago);
    });
  }
  
  function getEarliestVoteAttribute()
  {
    $vote = $this->votes()->orderBy('created_at')->first();
    if(!$vote) $vote = new Vote();
    return $vote;
  }
  
  function user()
  {
    if(!$this->fb_id) return null;
    $u = User::whereFbId($this->fb_id)->first();
    return $u;
  }
  
  function slug()
  {
    $slugify = new Slugify();
    return $slugify->slugify($this->name, '_');
  }

  function can_vote()
  {
    return $this->contest->can_vote();
  }


  function image_url($size='thumb')
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

