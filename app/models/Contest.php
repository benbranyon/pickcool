<?php
use Cocur\Slugify\Slugify;
  
class Contest extends Eloquent
{
  function candidates()
  {
    return $this->hasMany('Candidate');
  }
  
  function slug()
  {
    $names = [];
    foreach($this->candidates as $c)
    {
      $names[] = $c->name;
    }
    $names = join(' vs ', $names);
    $slugify = new Slugify();
    return $slugify->slugify($names, '_');
  }
}