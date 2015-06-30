<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
  public function getDates()
  {
    return [
      'created_at',
      'updated_at',
      'ends_at',
    ];
  }
  

  static $intervals = [0,24];
  
  function getTotalCharityDollarsAttribute()
  {
    $total = 0;
    foreach($this->candidates as $c)
    {
      if(!$c->charity_name) continue;
      $total++;
    }
    return $total * 300 * .25;
  }

  function getTotalCharityHoursAttribute()
  {
    $total = 0;
    foreach($this->candidates as $c)
    {
      if(!$c->charity_name) continue;
      $total++;
    }
    return $total * 4;
  }
    

  function getSmallUrlAttribute()
  {
    return r('contest.view', [$this->id, $this->slug, 'small']);
  }

  function getLargeUrlAttribute()
  {
    return r('contest.view', [$this->id, $this->slug, 'large']);
  }

  function getRealtimeUrlAttribute()
  {
    return r('contest.view', [$this->id, $this->slug, 'realtime']);
  }

  function getJoinUrlAttribute()
  {
    return r("contest.join", [$this->id])."?cancel=".urlencode(Request::url());
  }
  
  function getIsJoinableAttribute()
  {
    return $this->writein_enabled && !$this->is_ended && !$this->has_dropped;
  }
  
  function getCanJoinAttribute()
  {
    return $this->is_joinable && !$this->has_joined;
  }
  
  function getHasJoinedAttribute()
  {
    return $this->has_joined();
  }
  
  function has_joined($user=null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return null;
    return Candidate::whereUserId($user->id)->whereContestId($this->id)->first() != null;
  }

  function getRandomSponsorAttribute()
  {
    return $this->belongsToMany('Sponsor')->orderByRaw("RAND()")->first();
  }
  
  function getHasDroppedAttribute()
  {
    return $this->has_dropped();
  }
  
  function has_dropped($user = null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return false;
    $c = Candidate::whereUserId($user->id)->whereContestId($this->id)->first();
    if($c==null) return false;
    return !$c->is_active;
  }

  
  function getIsShareableAttribute()
  {
    return $this->password != true;
  }
  
  function getIsEndedAttribute()
  {
    return $this->ends_at!=null && ($this->ends_at->format('U') < time());
  }
  
  function getLoginUrlAttribute()
  {
    return r('contest.login', [$this->id, $this->slug]);
  }
  
  function authorize_user($user = null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return false;
    Session::put('contest_access_'.$this->id, $user->id);
    return true;
  }
  
  function getCanViewAttribute()
  {
    return $this->can_view();
  }
  
  function getCanEndAttribute()
  {
    return $this->ends_at != null;
  }
  
  function can_view($user=null)
  {
    if($this->password)
    {
      if(!$user) $user = Auth::user();
      if(!$user) return false;
      return Session::get('contest_access_'.$this->id) == $user->id;
    }
    return true;
  }
  
  function authorized_users()
  {
    return $this->belongsToMany('ContestUser')->whereHasPassedChallenge(true);
  }
  
  function getCanonicalUrlAttribute()
  {
    return r('contest.view', [$this->id, $this->slug]);
  }
  
  function getSlugAttribute()
  {
    return $this->slug();
  }
  
  static function add_vote_count_columns($columns)
  {
    foreach(self::$intervals as $interval)
    {
      //$columns[] = DB::raw("(select count(*) from votes v join candidates c on v.candidate_id = c.id where c.contest_id = contests.id and v.updated_at < utc_timestamp() - interval {$interval} hour) as vote_count_{$interval}");
      $columns[] = DB::raw("(select count(*) from votes v join candidates c on v.candidate_id = c.id where c.contest_id = contests.id and v.updated_at < utc_timestamp() - interval {$interval} hour) + (select COALESCE(SUM(b.vote_weight),0) from badges b inner join badge_candidate cb on b.id = cb.badge_id inner join candidates c on cb.candidate_id = c.id where c.contest_id = contests.id and cb.updated_at < utc_timestamp() - interval {$interval} hour) as vote_count_{$interval}");
    }
    return $columns;
  }
  
	public function newEloquentBuilder($query)
	{
    $builder = parent::newEloquentBuilder($query);
    $builder
      ->select($this->add_vote_count_columns(['contests.*']))
    ;
    return $builder;
	}
  
  static function hot()
  {
    self::$intervals[] = 72;
    $contests = self::query()
      ->whereIsArchived(false)
      ->whereRaw('(ends_at is null or ends_at > now())')
      ->havingRaw('vote_count_0 > vote_count_72')
      ->orderByRaw('vote_count_0 - vote_count_72 desc')
      
      ->get();
    return $contests;
  }
  
  static function recent()
  {
    $contests = self::query()
      ->whereIsArchived(false)
      ->whereRaw('ends_at > now()')
      ->orderBy('created_at', 'desc')
      ->get();
    return $contests;
  }

  static function top()
  {
    $contests = self::query()
      ->whereIsArchived(false)
      ->orderBy('vote_count_0', 'desc')
      ->get();
    return $contests;
  }
  

  
  function getHashTagAttribute()
  {
    return preg_replace("/[^A-Za-z0-9]/", "", ucwords($this->title));
  }
  
  function fb_candidates()
  {
    return $this->candidates()->whereNotNull('user_id')->get();
  }
  
  function standings()
  {
    return $this->candidates()->orderBy('contest_id')->orderBy('current_rank', 'asc')->get();
  }

  public function sponsors()
  {
    return $this->belongsToMany('Sponsor')->with('image')->withPivot('weight')->orderBy('weight');
  }
  
  function can_enter()
  {
    if($this->ends_at)
    {
      return $this->writein_enabled && $this->ends_at->gt(\Carbon::now());
    } else {
      return $this->writein_enabled;
    }
  }
  
  function getIsVoteableAttribute()
  {
    return $this->can_vote;
  }
  
  function getCanVoteAttribute()
  {
    return !$this->ends_at || $this->ends_at->gt(\Carbon::now());
  }
  
  function candidates()
  {
    return $this->hasMany('Candidate')->whereNotNull('image_id')->whereNull('dropped_at')->orderBy('vote_count_0', 'desc')->orderBy('first_voted_at', 'asc')->orderBy('created_at', 'asc')->with('image', 'images', 'badges');
  }

  function category()
  {
    return $this->belongsTo('Category');
  }

  function ranked_candidates($interval)
  {
    $old = Candidate::$intervals;
    Candidate::$intervals[] = $interval;
    $candidates = Candidate::whereContestId($this->id)->whereNotNull('image_id')->whereNull('dropped_at')->with('image')->get()->withRanks();
    Candidate::$intervals = $old;
    return $candidates;
  }
  
  function is_editable_by($user)
  {
    if(!$user) return false;
    return
      $user->id == $this->user_id || $this->is_moderator($user);
  }
  
  function is_moderator($user)
  {
    if(!$user) return false;
    return $user->is_admin;
  }
  

  function slug()
  {
    $slugify = new Slugify();
    return $slugify->slugify($this->title, '_');
  }
  
  function getCurrentWinnerAttribute()
  {
    $w = null;
    return $this->candidates->first();
  }
  
  function add_user($user = null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return null;
    
    $can = Candidate::whereUserId($user->id)->whereContestId($this->id)->first();
    $is_new = false;
    if(!$can)
    {
      $can = new Candidate();
      $can->contest_id = $this->id;
      $can->user_id = $user->id;
      $is_new = true;
    }
    $can->name = $user->full_name;
    $can->save();
    
    if($is_new)
    {
      $can = Candidate::find($can->id);
      Message::create([
        'user_id'=>$user->id,
        'subject'=>"Pick joined - pending verification",
        'body'=>View::make('messages.join_verify')->with([
          'candidate'=>$can,
          'contest'=>$can->contest,
        ]),
      ]);
    }
      
    $user->vote_for($can);

    return $can;
  }
}