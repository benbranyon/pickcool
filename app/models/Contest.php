<?php
  
class Contest extends EloquentBase
{
  function candidates()
  {
    return $this->hasMany('Candidate');
  }
}