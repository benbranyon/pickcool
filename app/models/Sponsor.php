<?php
use Cocur\Slugify\Slugify;
  
class Sponsor extends Eloquent
{
  function getImageUrlAttribute()
  {
    return $this->image_url();
  }
  
  function image_url($size=thumb)
  {
    return $this->image->image->url($size);
  }

  function image()
  {
    return $this->belongsTo('Image');
  }
}