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
  
  function can_enter()
  {
    if($this->ends_at)
    {
      return $this->writein_enabled && $this->ends_at->gt(\Carbon::now());
    } else {
      return $this->writen_enabled;
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