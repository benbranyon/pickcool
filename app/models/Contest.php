<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
  function candidates()
  {
    return $this->hasMany('Candidate');
  }
  
  function is_editable_by($user)
  {
    return
      $user->id == $this->user_id || $this->is_moderator($user);
  }
  
  function is_moderator($user)
  {
    return $user->is_admin;
  }
  
  function vs()
  {
    $names = [];
    foreach($this->candidates as $c)
    {
      $names[] = $c->name;
    }
    $names = join(' vs ', $names);
    return $names;
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
    return $slugify->slugify($this->vs(), '_');
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