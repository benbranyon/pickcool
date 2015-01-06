<?php
  
class Vote extends Eloquent
{
  function contest()
  {
    return $this->belongsTo('Contest');
  }
}
