<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
  function getIsEndedAttribute()
  {
    return $this->ends_at->format('U') < time();
  }
  
  function getLoginUrlAttribute()
  {
    return route('contest.login', [$this->id, $this->slug]);
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
    return route('contest.view', [$this->id, $this->slug]);
  }
  
  function getSlugAttribute()
  {
    return $this->slug();
  }
  
  static function hot()
  {
    $contests = Contest::join('votes', 'votes.contest_id', '=', 'contests.id')
      ->whereRaw('votes.created_at > now() - interval 72 hour')
      ->groupBy('contests.id')
      ->select(['contests.*', DB::raw('count(votes.id) as rank')])
      ->orderBy('rank', 'desc')
      ->get();
    return $contests;
  }
  
  static function recent()
  {
    $contests = Contest::query()
      ->orderBy('created_at', 'desc')
      ->with('candidates', 'candidates.votes')
      ->get();
    return $contests;
  }

  static function top()
  {
    $contests = Contest::join('votes', 'votes.contest_id', '=', 'contests.id', 'left outer')
      ->groupBy('contests.id')
      ->select(['contests.*', DB::raw('count(votes.id) as rank')])
      ->orderBy('rank', 'desc')
      ->get();
    return $contests;
  }
  

  
  function toHashTag()
  {
    return preg_replace("/[^A-Za-z0-9]/", "", ucwords($this->title));
  }
  
  function fb_candidates()
  {
    return $this->candidates()->whereNotNull('fb_id')->get();
  }
  
  function calc_ranks($ago = '0 day', $should_save = false)
  {
    $key = "contest_calc_current_ranks_{$this->id}";
    return Lock::go($key, function() use($ago, $should_save) {
      $this->vote_count = 0;
      $this->vote_count_hot = 0;
      $candidates = $this->candidates()->get();
      $winner = $candidates[0];
      foreach($candidates as $candidate)
      {
        $vc = $candidate->votes_ago($ago)->count();
        $candidate->total_votes = $vc;
        $this->vote_count += $candidate->total_votes;
        $this->vote_count_hot += ($candidate->total_votes - $candidate->votes_ago('3 day')->count());
      }
      if($should_save) $this->save();
      $candidates->sort(function($a,$b) {
        $atv = $a->total_votes;
        $btv = $b->total_votes;
        if($atv>0 && $btv==0) return -1;
        if($atv==0 && $btv>0) return 1;
        if($atv==0 && $btv==0)
        {
          $created = $a->created_at->timestamp - $b->created_at->timestamp;
          return $created;
        }
        if($atv==$btv)
        {
          $earliest_vote = $a->earliest_vote->created_at->timestamp - $b->earliest_vote->created_at->timestamp;
          return $earliest_vote;
        }
        return $btv - $atv;
      });
      $rank=1;
      foreach($candidates as $candidate)
      {
        $candidate->current_rank = $rank++;
        if($should_save) $candidate->save();
      }
      $new_winner = $candidates->first();
      Log::info("Old winner ". $winner->id);
      Log::info("New winner ". $new_winner->id);
      if($winner->id != $new_winner->id)
      {
        $url = route('contest.view', [$this->id, $this->slug()]);
        Log::info("New winner, calling Facebook for $url");
        $client = new \GuzzleHttp\Client();
        $client->post('http://graph.facebook.com', ['query'=>[
          'id'=>$url,
          'scrape'=>'true',
        ]]);
      }
      return $candidates;
    });

  }
  
  function standings()
  {
    return $this->candidates()->orderBy('contest_id')->orderBy('current_rank', 'asc')->get();
  }

  public function sponsors()
  {
    return $this->belongsToMany('Sponsor')->withPivot('weight')->orderBy('weight');
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
    return $this->hasMany('Candidate')->orderBy('current_rank', 'asc');
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
  
  function current_winner()
  {
    $w = null;
    foreach($this->candidates as $c)
    {
      if(!$w) $w = $c;
      if($c->votes()->count() > $w->votes()->count())
      {
        $w = $c;
      }
    }
    return $w;
  }
}