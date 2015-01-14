<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
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
    
  function getRealtimeUrlAttribute()
  {
    return r('contest.realtime', [$this->id, $this->slug]);
  }

  function getJoinUrlAttribute()
  {
    return r("contest.join", [$this->id])."?cancel=".urlencode(Request::url());
  }
  
  function getCanJoinAttribute()
  {
    return $this->writein_enabled && !$this->is_ended && !$this->has_joined;
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
      $columns[] = DB::raw("(select count(*) from votes v join candidates c on v.candidate_id = c.id where c.contest_id = contests.id and v.updated_at < utc_timestamp() - interval {$interval} hour) as vote_count_{$interval}");
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
      ->havingRaw('vote_count_0 > vote_count_72')
      ->orderByRaw('vote_count_0 - vote_count_72 desc')
      ->get();
    return $contests;
  }
  
  static function recent()
  {
    $contests = self::query()
      ->orderBy('created_at', 'desc')
      ->get();
    return $contests;
  }

  static function top()
  {
    $contests = self::query()
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
  
  public function getDates()
  {
    return [
      'created_at',
      'updated_at',
      'ends_at',
    ];
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
    return $this->hasMany('Candidate')->whereNull('dropped_at')->orderBy('vote_count_0', 'desc')->orderBy('first_voted_at', 'asc')->orderBy('created_at', 'asc')->with('image');
  }

  function ranked_candidates($interval)
  {
    $old = Candidate::$intervals;
    Candidate::$intervals[] = $interval;
    $candidates = Candidate::whereContestId($this->id)->whereNull('dropped_at')->with('image')->get()->withRanks();
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
    $i = \Image::from_url($user->profile_image_url,true);
    $can->name = $user->full_name;
    $can->image_id = $i->id;
    $can->save();
    $user->vote_for($can);
    
    $client = new \GuzzleHttp\Client();
    $client->post('https://graph.facebook.com/v2.2', ['query'=>[
      'access_token'=>'1497159643900204|DJ6wUZoCJWlOWDHegW1fFNK9r-M',
      'id'=>$can->canonical_url($this),
      'scrape'=>'true',
    ]]);

    
    if($is_new)
    {
      $u = $user;
      $c = $can;
      $contest = $this;
      $vars = [
        'subject'=>"[{$contest->title}] - Entry Confirmation",
        'to_email'=>$u->email,
        'candidate_full_name'=>$u->full_name,
        'candidate_first_name'=>$u->first_name,
        'contest_name'=>$contest->title,
        'candidate_url'=>$c->canonical_url,
        'help_url'=>'http://pick.cool/help/sharing',
        'call_to_action'=>"Vote {$u->full_name} in {$contest->name}",
        'hashtags'=>['PickCool', $u->hash_tag, $contest->hash_tag],
        'sponsors'=>$contest->sponsors,
      ];
      $message = $contest->password ? 'emails.candidate-join-pick-earlybird' : 'emails.candidate-join-pick';
      \Mail::send($message, $vars, function($message) use ($vars)
      {
          $message->to($vars['to_email'])->subject($vars['subject']);
      });      
    }
    return $can;
  }
}