<?php
  
class Candidate extends Eloquent
{
  function image()
  {
    return $this->belongsTo('Image');
  }

    public function votes()
    {
        return $this->hasMany('Vote');
    }
}