<?php
  
class Candidate extends Eloquent
{
  function image()
  {
    return $this->belongsTo('Image');
  }
}