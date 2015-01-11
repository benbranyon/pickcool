<?php
use Cocur\Slugify\Slugify;
  
class Candidate extends Eloquent
{
  public static $intervals = [0,24];
  public static $on_fire_threshold = .1;

  public function getDates()
  {
    return ['created_at', 'updated_at', 'first_voted_at', 'dropped_at'];
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
  
	public function newCollection(array $models = array())
	{
		return new RankedCandidateCollection($models);
	}
  
  function add_columns($columns)
  {
    foreach(self::$intervals as $interval)
    {
      $columns[] = DB::raw("(select count(id) from votes where candidate_id = candidates.id and updated_at < utc_timestamp() - interval {$interval} hour) as vote_count_{$interval}");
    }
    $columns[] = DB::raw("(select updated_at from votes where candidate_id = candidates.id order by updated_at asc limit 1) as first_voted_at");
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
    return $this->user_id == $user->id;
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
    return route('candidates.vote', [$this->id, 'cancel'=>$this->canonical_url]);
  }
  
  function getUnvoteUrlAttribute()
  {
    return route('candidates.unvote', [$this->id]);
  }
  
  function getCanonicalUrlAttribute()
  {
    return $this->canonical_url($this->contest);
  }
    
  function canonical_url($contest)
  {
    return route('contest.candidate.view', [$contest->id, $contest->slug(), $this->id, $this->slug]);
  }
  
  function getUnfollowUrlAttribute()
  {
    return route('contest.candidate.unfollow', [$this->id]);
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
  
  function image_url($size=thumb)
  {
    return route('home').$this->image->image->url($size);
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
