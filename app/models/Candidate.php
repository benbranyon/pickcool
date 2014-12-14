<?php
use Cocur\Slugify\Slugify;
  
class Candidate extends Eloquent
{
  
  function delete()
  {
    $this->votes->delete();
  }
  
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
  
  function is_editable_by($user)
  {
    return $this->contest->is_editable_by($user);
  }
  
  function is_moderator($user)
  {
    return $this->contest->is_moderator($user);
  }
  
}