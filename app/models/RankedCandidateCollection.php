<?php
  
class RankedCandidatecollection extends Illuminate\Support\Collection
{
	public function __construct(array $items = array())
	{
    parent::__construct($items);
	}
  
  public function withRanks()
  {
    $start = microtime(true);
    $vote_count_intervals = Candidate::$intervals;

    $sort = $this->items;
    foreach($vote_count_intervals as $interval)
    {
      usort($sort, function($a,$b) use($interval) {
        $vote_count = "vote_count_{$interval}";

        $atv = $a->$vote_count;
        $btv = $b->$vote_count;
        if($atv>0 && $btv==0) return -1;
        if($atv==0 && $btv>0) return 1;
        if($atv==0 && $btv==0)
        {
          $created =  $a->created_at->timestamp - $b->created_at->timestamp;
          return $created;
        }
        if($atv==$btv)
        {
          $earliest_vote = $a->first_voted_at->timestamp - $b->first_voted_at->timestamp;
          if($earliest_vote!=0) return $earliest_vote;
          $created =  $a->created_at->timestamp - $b->created_at->timestamp;
          if($created!=0) return $created;
          return $a->id - $b->id; // last resort - which candidate it to the DB first
        }
        return $btv - $atv;
      });
      $rank=1;
      $rank_key = "rank_{$interval}";
      foreach($sort as $c)
      {
        $c->$rank_key = $rank++;
      }
    }
    Log::info(microtime(true)-$start);
    return $this;
  }
  
}