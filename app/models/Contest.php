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
  
  function generate_standings($ago='0 day')
  {
    $candidates = $this->candidates()->get();
    foreach($candidates as $candidate)
    {
      $candidate->total_votes = $candidate->votes_ago($ago)->count();
      $candidate->save();
    }
    $candidates->sort(function($a,$b) {
      if($a->total_votes==$b->total_votes) return 0;
      return ($a->total_votes < $b->total_votes) ? 1 : -1;
    });
    $rank=1;
    foreach($candidates as $candidate)
    {
      $candidate->previous_rank = $candidate->current_rank;
      $candidate->current_rank = $rank++;
      $candidate->save();
    }
    return $candidates;
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
    return $this->hasMany('Candidate');
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
  
  function candidateNamesForHumans($exclude_id=null, $join = 'or') {
    $names = [];
    foreach($this->candidates as $c)
    {
      if($c->id == $exclude_id) continue;
      $names[] = $c->name;
    }
    if(count($names))
    {
      $names[count($names)-1] = "{$join} {$names[count($names)-1]}";
    }
    $names = join(', ', $names);
    return $names;
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