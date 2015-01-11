<?php
  
class ActivityLog extends Eloquent
{
  
}

ActivityLog::saving(function($obj) {
  if(!$obj->session_id)
  {
    $obj->session_id = Session::getId();
  }
  if(!$obj->action)
  {
    $obj->action = Request::url();
  }
  if(!$obj->user_id && Auth::user())
  {
    $obj->user_id = Auth::user()->id;
  }
});