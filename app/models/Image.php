<?php
  
class Image extends BenAllfree\LaravelStaplerImages\Image
{
  public function getDates()
  {
    return [
      'image_updated_at',
      'created_at',
      'updated_at',
      'screened_at',
    ];
  }
  
  function candidate()
  {
    return $this->belongsTo('Candidate');
  }
  
  function getContestAttribute()
  {
    return $this->candidate->contest;
  }
}
