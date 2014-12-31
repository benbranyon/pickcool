<?php
namespace Api\V1;
  
use \Input;
use \Contest;
use \ApiSerializer;
use \Candidate;
use \Auth;
use \BaseController;
use \DB;

class ContestController extends BaseController
{
  function top()
  {
    $contests = Contest::join('votes', 'votes.contest_id', '=', 'contests.id', 'left outer')
      ->whereNull('password')
      ->groupBy('contests.id')
      ->select(['contests.*', DB::raw('count(votes.id) as rank')])
      ->orderBy('rank', 'desc')
      ->get();
    return ApiSerializer::ok($contests);
  }
  
  function hot()
  {
    $contests = Contest::join('votes', 'votes.contest_id', '=', 'contests.id')
      ->whereRaw('votes.created_at > now() - interval 72 hour')
      ->whereNull('password')
      ->groupBy('contests.id')
      ->select(['contests.*', DB::raw('count(votes.id) as rank')])
      ->orderBy('rank', 'desc')
      ->get();
    return ApiSerializer::ok($contests);
  }
  
  function recent()
  {
    $contests = Contest::query()
      ->whereNull('password')
      ->orderBy('created_at', 'desc')
      ->with('candidates', 'candidates.votes')
      ->get();
    return ApiSerializer::ok($contests);
  }

  function local()
  {
    $contests = Contest::query()
      ->select(['contests.*', DB::raw('(select count(votes.id) from votes where contest_id = contests.id and votes.created_at > utc_timestamp() - interval 72 hour) as vote_count_72')])
      ->with('candidates', 'candidates.votes', 'candidates.image', 'sponsors')
      ->get();
    return ApiSerializer::ok($contests);
  }
  
  function get($id)
  {
    $c = Contest::find($id);
    if(!$c)
    {
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    return ApiSerializer::ok(ApiSerializer::serialize($c, 'thumb'));
  }
  
}