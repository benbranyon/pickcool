<?php
namespace Api\V1;
  
use \Input;
use \Contest;
use \ApiSerializer;
use \Candidate;
use \Auth;
use \BaseController;

class ContestViewController extends BaseController
{
  function join()
  {
    $contest_id = Input::get('contest_id');
    $c = Contest::find($contest_id);
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    if(!$c)
    {
      return ApiSerializer::error(API_ERR_LOOKUP);
    }
    if(!$c->writein_enabled)
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    
    $can = Candidate::whereFbId(Auth::user()->fb_id)->first();
    if(!$can)
    {
      $can = new Candidate();
      $can->contest_id = $c->id;
      $can->fb_id = Auth::user()->fb_id;
      $can->buy_url = 'x';
      $can->buy_text = 'x';
    }
    $i = Image::from_url(Auth::user()->profile_image_url());
    $can->name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
    $can->image_id = $i->id;
    $can->save();
    return ApiSerializer::ok($can->contest);
  }
  
}