<?php
namespace Api\V1;
  
use \Input;
use \Contest;
use \ApiSerializer;
use \Candidate;
use \Auth;
use \BaseController;

class UserController extends BaseController
{
  function get()
  {
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }
    return ApiSerializer::ok(Auth::user());
  }
  
}