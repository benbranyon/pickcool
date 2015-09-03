<?php
use Cocur\Slugify\Slugify;
  
class Candidate extends Eloquent
{
  public static $on_fire_threshold = .1;

  public function getDates()
  {
    return ['created_at', 'updated_at', 'first_voted_at', 'dropped_at'];
  }
  
  function manage_url($cmd_name, $cmd_id)
  {
    return r('contests.candidate.manage', [$this->contest->id, $this->contest->slug, $this->id, $this->slug, $cmd_name, $cmd_id]);
  }
  
  function getAddImageUrlAttribute()
  {
    return r('contests.candidates.images.add', [$this->contest->id, $this->contest->slug, $this->id, $this->slug]);
  }
  
  function getIsOwnerAttribute()
  {
    return $this->is_owner();
  }
  
  function is_owner($user=null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return false;
    return $user->id == $this->user_id;
  }

  public function images()
  {
    return $this->hasMany('Image');
  }
  
  public function getWeightedImagesAttribute()
  {
    $candidate = $this;
    $images = $this->images()->get()->sort(function($a,$b) use($candidate) {
      if($candidate->image_id == $a->id) return -1;
      if($candidate->image_id == $b->id) return 1;
      $weight = $a->weight - $b->weight;
      if($weight) return $weight;
      return $a->created_at->timestamp - $b->created_at->timestamp;
    })->values();
    return $images;
  }
  
  function getHasPendingImagesAttribute()
  {
    return $this->images()->whereNull('screened_at')->count() > 0;
  }

  function getHasFeaturedImageAttribute()
  {
    return $this->images()->where('status', '=', 'featured')->count() > 0;
  }
  
  function getRefreshUrlAttribute()
  {
    return r('contests.candidate.refresh', ['contest_id'=>$this->contest->id, 'contest_slug'=>$this->contest->slug, 'candidate_id'=>$this->id, 'candidate_slug'=>$this->slug]);
  }
  
  function getVotersUrlAttribute()
  {
    return r('contests.candidate.voters.view', ['contest_id'=>$this->contest->id, 'contest_slug'=>$this->contest->slug, 'candidate_id'=>$this->id, 'candidate_slug'=>$this->slug]);
  }
  
  public function getIsActiveAttribute()
  {
    return $this->dropped_at == null;
  }
  
  

  
  function getIsOnFireAttribute()
  {
    $delta = $this->vote_count_0 - $this->vote_count_24;
    if($delta<10) return false;
    return ($delta/$this->vote_count_0) > self::$on_fire_threshold;
  }
  
  function getIsGiverAttribute()
  {
    return trim($this->charity_name)!=false;
  }
  
  function rank_change_since($interval)
  {
    $rank_key = "rank_{$interval}";
    return $this->$rank_key - $this->rank_0;
  }
  
  function vote_change_since($interval)
  {
    $vote_key = "vote_count_{$interval}";
    return $this->vote_count_0 - $this->$vote_key;
  }
  
  function add_columns($columns)
  {
    if(Auth::user())
    {
      $user_id = Auth::user()->id;
      $columns[] = DB::raw("(select id from votes where candidate_id = candidates.id and user_id = {$user_id}) as current_user_vote_id");
    }
    return $columns;
  }
  
	public function newEloquentBuilder($query)
	{
    $builder = parent::newEloquentBuilder($query);
    $builder->select($this->add_columns(['candidates.*']));
    return $builder;
	}
  
  
  function getAfterJoinUrlAttribute()
  {
    return r("contests.candidates.after_join", [$this->id]);
  }

  function getAfterVoteUrlAttribute()
  {
    return r('candidates.after_vote', [$this->id]);
  }
  
  function getLoginUrlAttribute()
  {
    return r('facebook.authorize', ['success'=>$this->canonical_url, 'cancel'=>$this->canonical_url]);
  }
  
  function getIsWriteinAttribute()
  {
    return $this->is_writein();
  }
  function is_writein($user=null)
  {
    return $this->is_owner($user);
  }
  
  function getIsVoteableAttribute()
  {
    return $this->contest->is_voteable;
  }
  
  function getIsUserVoteAttribute()
  {
    return $this->current_user_vote_id != null;
  }
  
  function getVoteUrlAttribute()
  {
    return r('candidates.vote', [$this->id, 'cancel'=>$this->canonical_url]);
  }
  
  function getUnvoteUrlAttribute()
  {
    return r('candidates.unvote', [$this->id]);
  }
  
  function getCanonicalUrlAttribute()
  {
    return $this->canonical_url($this->contest);
  }
    
  function canonical_url($contest)
  {
    return r('contests.candidate.view', [$contest->id, $contest->slug(), $this->id, $this->slug]);
  }
  
  function getUnfollowUrlAttribute()
  {
    return r('contests.candidate.unfollow', [$this->id]);
  }
  
  function getUserAttribute()
  {
    if(!$this->user_id) return null;
    $u = User::find($this->user_id)->first();
    return $u;
  }
  
  function getSlugAttribute()
  {
    $slugify = new Slugify();
    return $slugify->slugify($this->name, '_');
  }

  function getImageUrlAttribute()
  {
    return $this->image_url();
  }
  
  function image_url($size='thumb')
  {
    $url = r('home').$this->image->image->url($size);
    return $url;
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

  public function badges()
  {
    return $this->belongsToMany('Badge')->withPivot('charity_name', 'charity_url', 'charity_percent');
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
  
  static function open_active_candidates()
  {
    return    self::active_candidates()
      ->whereRaw('contests.ends_at >= utc_timestamp()');
  }
  
  static function closed_active_candidates()
  {
    return    self::active_candidates()
      ->whereRaw('contests.ends_at < utc_timestamp()');
  }
  
  static function active_candidates()
  {
    return    Candidate::query()
      ->join('contests', 'contests.id', '=', 'candidates.contest_id')
      ->whereNotNull('image_id')
      //->whereNull('dropped_at')
      ->whereIsArchived(0);
  }
  
  
}

Candidate::saved(function($candidate) {
  Flatten::flushRoute('contests.live');
  Flatten::flushRoute('contests.archived');
  Contest::calc_stats();
  
});
