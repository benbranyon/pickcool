<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
  public static $intervals = [0,1,12,24,72,120,240];
  
  var $_current_user_candidate_id = null;

  function nextContest()
  {
    $today = Carbon::now();
    if ($this->state != null && $this->state != '') {
      
      $nextContest = Contest::where('state','=', $this->state)
        ->where('id','!=',$this->id)
        ->whereNotNull('ends_at')
        ->where('ends_at','>=',$today)
        ->orderByRaw("RAND()")->first();
      
      if ($nextContest == null) {
        $nextContest = Contest::where('id','!=',$this->id)
          ->whereNotNull('ends_at')
          ->where('ends_at','>=',$today)
          ->orderByRaw("RAND()")->first();
      }
      
    } else {
      $nextContest = Contest::where('id','!=',$this->id)
        ->whereNotNull('ends_at')
        ->where('ends_at','>=',$today)
        ->orderByRaw("RAND()")->first();
    }

    return $nextContest;
  }

  public function getDates()
  {
    return [
      'created_at',
      'updated_at',
      'ends_at',
    ];
  }
  
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
  
  public function votes()
  {
      return $this->hasMany('Vote');
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
  
  static function calc_stats()
  {
    $sqls = [];
    
    $sqls[] = "update candidates set first_voted_at = (select min(updated_at) from votes v where v.candidate_id = candidates.id)";
    foreach(self::$intervals as $interval)
    {
      $sqls[] = "
        update candidates c set
        	vote_count_{$interval} = 
            (select count(v.id) from votes v where v.candidate_id = c.id and v.updated_at <= utc_timestamp() - interval {$interval} hour) 
            + 
            ifnull(
              (select b.vote_weight from badges b join badge_candidate bc on b.id = bc.badge_id where bc.candidate_id = c.id and bc.updated_at <= utc_timestamp() - interval {$interval} hour)
              ,0)
      ";
      $sqls[] = "set @rn:=0;";
      $sqls[] = "set @old_contest_id:=0";
      $sqls[] = "
        update candidates c join
         (
          select 
          	contest_id, 
          	id as candidate_id,
          	case 
          		when @old_contest_id <> contest_id and @old_contest_id:=contest_id then @rn:=1
          		when @old_contest_id = contest_id then (@rn:=@rn+1)
          	end AS rank, 
          	vote_count_{$interval}
          from 
          	candidates 
          order by 
          	contest_id, 
          	vote_count_{$interval} desc, 
          	first_voted_at asc, 
          	id asc
         ) d
         on c.id = d.candidate_id
         set c.rank_{$interval} = d.rank;
      ";
      $sqls[] = "
        update contests set vote_count_{$interval} = (select sum(vote_count_{$interval}) from candidates where contest_id = contests.id)
      ";
    }
    
    foreach($sqls as $sql)
    {
      $sql = preg_replace('/\s*\n\s*/', ' ', $sql);
      DB::statement($sql);
    }
  }

  
  static function live()
  {
    $contests = self::query()
      ->whereIsArchived(false)
      ->whereRaw('(ends_at is null or ends_at > utc_timestamp())')
      ->orderByRaw('vote_count_0 - vote_count_72 desc');
    return $contests;
  }
  
  static function archived()
  {
    $contests = self::query()
      ->whereIsArchived(false)
      ->whereRaw('(ends_at <= utc_timestamp())')
      ->orderBy('vote_count_0', 'desc');
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
    return $this->hasMany('Candidate')->whereNotNull('image_id')->whereNull('dropped_at')->orderBy('rank_0')->with('image', 'images', 'badges');
  }

  function category()
  {
    return $this->belongsTo('Category');
  }

  function ranked_candidates($interval)
  {
    return $this->candidates()->orderBy('rank_'.$interval);
  }
  
  function getCurrentUserCandidateIdAttribute()
  {
    if($this->_current_user_candidate_id!==null) return $this->_current_user_candidate_id;
    if(!Auth::user()) return null;
    $vote = Auth::user()->current_vote_for($this);
    if(!$vote) return $this->_current_user_candidate_id = false;
    return $this->_current_user_candidate_id = $vote->candidate_id;
  }

  function getCurrentUserCandidateAttribute()
  {
    if($this->_current_user_candidate!==null) return $this->_current_user_candidate;
    if(!$this->current_user_candidate_id) return null;
    return Candidate::find($this->current_user_candidate_id);
  }

  
  function getIsEditableAttribute()
  {
    return $this->is_editable_by(Auth::user());
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


Contest::saved(function() {
  Flatten::flushRoute('contests.hot');
  Flatten::flushRoute('contests.new');
  Flatten::flushRoute('contests.top');
  Contest::calc_stats();
});