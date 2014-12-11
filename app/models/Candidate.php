<?php
use Cocur\Slugify\Slugify;
  
class Candidate extends Eloquent
{
  function image_url($size='thumb')
  {
    return route('image.view', [$this->image->id, $size]);
  }
  
  function image()
  {
    return $this->belongsTo('Image');
  }
  
  function contest()
  {
    return $this->belongsTo('Contest');
  }

  public function votes()
  {
    return $this->hasMany('Vote');
  }
}