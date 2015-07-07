<?php
  
class Vote extends Eloquent
{
  function contest()
  {
    return $this->belongsTo('Contest');
  }
  
  function candidate()
  {
    return $this->belongsTo('Candidate');
  }
}


Vote::saved(function($vote) {
  Flatten::flushRoute('contests.hot');
  Flatten::flushRoute('contests.new');
  Flatten::flushRoute('contests.top');
  Contest::calc_stats();
});