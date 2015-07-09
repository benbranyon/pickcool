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
  
  static function calc_votes_ahead($candidate_ids=[])
  {
    $extra = '';
    if(count($candidate_ids)>0)
    {
      $candidate_ids = join(',',$candidate_ids);
      $extra = "and v1.candidate_id in ({$candidate_ids})";
    }
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
          $extra
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
  Vote::query()
    ->whereCandidateId($vote->getOriginal()['candidate_id'])
    ->where('voted_at', '<', $vote->getOriginal()['voted_at'])
    ->whereNotIn('user_id', $vote->contest->candidates->lists('user_id'))
    ->update(['votes_ahead'=>DB::raw('votes_ahead - 1')]);

  Vote::query()
    ->whereCandidateId($vote->candidate_id)
    ->where('voted_at', '<', $vote->voted_at)
    ->whereNotIn('user_id', $vote->contest->candidates->lists('user_id'))
    ->update(['votes_ahead'=>DB::raw('votes_ahead + 1')]);
  
  $pending_points = Vote::query()
    ->join('contests', 'votes.contest_id', '=', 'contests.id')
    ->whereRaw('contests.ends_at > utc_timestamp()')
    ->where('votes.user_id', '=', $vote->user_id)
    ->sum('votes_ahead');
  
  $vote->user->pending_points = $pending_points;
  $vote->user->save();
  
  Flatten::flushRoute('contests.live');
  Flatten::flushRoute('contests.archived');
  Contest::calc_stats();
});