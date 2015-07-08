<?php
  
class Vote extends Eloquent
{
  use RawDb;

  public function getDates()
  {
    return ['created_at', 'updated_at', 'voted_at'];
  }

  function contest()
  {
    return $this->belongsTo('Contest');
  }
  
  function candidate()
  {
    return $this->belongsTo('Candidate');
  }
  
  function user()
  {
    return $this->belongsTo('User');
  }
  
  static function calc_votes_ahead()
  {
    $sqls = [];
    $sqls[] = "drop temporary table if exists eligible_votes;";
    $sqls[] = "create temporary table eligible_votes like votes;";
    $sqls[] = "insert into eligible_votes select * from votes v where v.id not in (select v.id from votes v join candidates c on c.id = v.candidate_id and c.user_id = v.user_id);";
    $sqls[] = "drop temporary table if exists eligible_votes2;";
    $sqls[] = "create temporary table eligible_votes2 like votes;";
    $sqls[] = "insert into eligible_votes2 select * from votes;";
    $sqls[] = "
    update votes v join (
    	select 
    		v1.id,
    		count(v2.id) as votes_ahead
    	from 
    		contests c 
    			join 
    		eligible_votes v1  
    			on 
    		v1.contest_id = c.id 
    			join 
    		eligible_votes2 v2 
    			on 
    		v1.contest_id = v2.contest_id 
    			and 
    		v1.candidate_id = v2.candidate_id 
    			and 
    		v1.voted_at < v2.voted_at 
    			and
    		v1.user_id != v2.user_id
    	group by
    		v1.id
    		) d on v.id = d.id
		set v.votes_ahead = d.votes_ahead

    ";
    self::execute($sqls);
  }
}


Vote::saved(function($vote) {
  Flatten::flushRoute('contests.hot');
  Flatten::flushRoute('contests.new');
  Flatten::flushRoute('contests.top');
  Contest::calc_stats();
});