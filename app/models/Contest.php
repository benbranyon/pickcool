<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{

  function getJoinUrlAttribute()
  {
    return route("contest.join", [$this->id])."?cancel=".urlencode(Request::url());
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
    return Candidate::whereFbId($user->fb_id)->whereContestId($this->id)->first() != null;
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
  

  
  function getHashTagAttribute()
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
        $this->vote_count += ($candidate->total_votes+2);
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
          $created =  $a->created_at->timestamp - $b->created_at->timestamp;
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
        $candidate->total_votes+=2;
        if($should_save) $candidate->save();
        $earliest_vote = "(none)";
        if($candidate->earliest_vote) $earliest_vote = $candidate->earliest_vote->created_at;
        Log::info("
        {$candidate->name}: ID {$candidate->id}, {$candidate->total_votes} votes, Earliest vote {$earliest_vote}, Created {$candidate->created_at}, Rank {$candidate->current_rank}
        ");
      }
      $new_winner = $candidates->first();
      if($winner->id != $new_winner->id)
      {
        Log::info("
        {$this->title}
        Old Winner: {$winner->name} {$winner->id}
        New Winner: {$new_winner->name} {$new_winner->id}
        ");
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
  
  function getCurrentWinnerAttribute()
  {
    $w = null;
    return $this->candidates->first();
  }
  
  function add_user($user = null)
  {
    if(!$user) $user = Auth::user();
    if(!$user) return null;
    
    $can = Candidate::whereFbId($user->fb_id)->whereContestId($this->id)->first();
    $is_new = false;
    if(!$can)
    {
      $can = new Candidate();
      $can->contest_id = $this->id;
      $can->fb_id = $user->fb_id;
      $is_new = true;
    }
    $i = \Image::from_url($user->profile_image_url,true);
    $can->name = $user->full_name;
    $can->image_id = $i->id;
    $can->save();
    $user->vote_for($can);
    
    $client = new \GuzzleHttp\Client();
    $client->post('http://graph.facebook.com', ['query'=>[
      'id'=>$can->canonical_url,
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