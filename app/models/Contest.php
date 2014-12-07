<?php
  
class Contest extends Eloquent
{
  function candidates()
  {
    return $this->hasMany('Candidate');
  }
}