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
    if(!$c->can_enter())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    $can = Candidate::whereFbId(Auth::user()->fb_id)->whereContestId($c->id)->first();
    $is_new = false;
    if(!$can)
    {
      $can = new Candidate();
      $can->contest_id = $c->id;
      $can->fb_id = Auth::user()->fb_id;
      $can->buy_url = 'x';
      $can->buy_text = 'x';
      $is_new = true;
    }
    $i = \Image::from_url(Auth::user()->profile_image_url(true),true);
    $can->name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
    $can->image_id = $i->id;
    $can->save();
    
    if($is_new)
    {
      $u = Auth::user();
      $c = $can;
      $contest = $c->contest;
      $vars = [
        'subject'=>"[{$contest->title}] - Entry Confirmation",
        'to_email'=>$u->email,
        'candidate_full_name'=>$u->full_name(),
        'candidate_first_name'=>$u->first_name,
        'contest_name'=>$contest->title,
        'candidate_url'=>$c->share_url(),
        'help_url'=>'http://pick.cool/help/sharing',
        'call_to_action'=>"Vote {$u->full_name()} in {$contest->name}",
        'hashtags'=>['PickCool', $u->toHashTag(), $contest->toHashTag()],
        'sponsors'=>$contest->sponsors,
      ];
      $message = $contest->password ? 'emails.candidate-join-pick-earlybird' : 'emails.candidate-join-pick';
      \Mail::send($message, $vars, function($message) use ($vars)
      {
          $message->to($vars['to_email'])->subject($vars['subject']);
      });      
    }
    return ApiSerializer::ok($can->contest);
  }
  
}