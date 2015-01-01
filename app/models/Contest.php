<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
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
        $candidate->total_votes = $candidate->votes_ago($ago)->count();
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
        Log::info("New winner, calling Facebook");
        $client = new \GuzzleHttp\Client();
        $client->post('http://graph.facebook.com', ['query'=>[
          'id'=>route('contest.view', [$new_winner->id]),
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
  
  function can_vote()
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