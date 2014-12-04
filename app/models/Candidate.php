<?php
  
class Candidate extends EloquentBase
{
  function image()
  {
    return $this->belongsTo('Image');
  }
}