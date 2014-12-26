<?php
namespace Api\V1;
  
use \Input;
use \Contest;
use \ApiSerializer;
use \Candidate;
use \Auth;
use \BaseController;

class VoteController extends BaseController
{
  function vote()
  {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    
    $c = Candidate::find(Input::get('c'));
    if(!$c)
    {
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    if(!$c->can_vote())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    Auth::user()->vote_for($c->id);
    return ApiSerializer::ok($c->contest);
  }
  
  function unvote()
  {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    
    $c = Candidate::find(Input::get('c'));
    if(!$c)
    {
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    if(!$c->can_vote())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    
    Auth::user()->unvote_for($c->id);
    return ApiSerializer::ok($c->contest);    
  }
  
}