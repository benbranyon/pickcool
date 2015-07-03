<?php

class UserContextController extends \BaseController {
  function __construct()
  {
    $this->content = View::make('usercontext.app.header-js');
  }
  
  function go()
  {
    if(!Auth::user())
    {
      $response = Response::make($this->content, 200);
      $response->header('Content-Type', 'application/javascript');
      return $response;
    }
    
    $sql = sprintf("
      select 
        c.id, 
        v.candidate_id as current_user_candidate_id,
        (%d or c.user_id = %d) as is_editable
      from
      	contests c
      	left outer join 
      	votes v
      	on c.id = v.contest_id and v.user_id = 1
      where
        c.is_archived is not null
      ",
      Auth::user()->is_admin,
      Auth::user()->id
    );
    
    $recs = DB::select(DB::raw($sql));
    
    $this->content .= View::make('usercontext.contests.list')->with('recs', $recs);

    $response = Response::make($this->content, 200);
    $response->header('Content-Type', 'application/javascript');
    return $response;
  }
}
