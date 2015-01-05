<?php
  
class Vote extends Eloquent
{
  function contest()
  {
    return $this->belongsTo('Contest');
  }
}

Vote::saved(function($obj) {
  $obj->contest->calc_ranks('0 day', true);
});

Vote::deleted(function($obj) {
  $obj->contest->calc_ranks('0 day', true);
});