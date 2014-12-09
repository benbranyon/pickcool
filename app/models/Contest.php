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
    $slugify = new Slugify();
    return $slugify->slugify($this->title, '_');
  }
}