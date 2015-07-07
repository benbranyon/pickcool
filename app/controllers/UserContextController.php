<?php

use Illuminate\Http\Response as IlluminateResponse;

class ExtendedResponse extends IlluminateResponse
{
  function addContent($s)
  {
    $this->setContent($this->getOriginalContent().$s);
    return $this;
  }
}
class UserContextController extends \BaseController {
  function __construct()
  {
    $this->content = View::make('usercontext.app.header-js');
    $this->response = new ExtendedResponse($this->content, 200, ['Content-Type'=>'application/javascript']);
    $this->afterFilter(function() {
      foreach(['success', 'warning', 'danger'] as $kind) Session::forget($kind);
    });
  }
  
  function go()
  {
    if(!Auth::user()) return $this->response->addContent('/* Not authenticated */');
    
    $route_name = Input::get('r', null);
    if(!$route_name) return $this->response->addContent("/* No route specified */");
    
    $params = Input::get('p',[]);
    
    switch($route_name)
    {
      case 'contests.live':
      case 'contests.archived':
        $this->contest_listing();
        break;
      case 'contest.view':
        $contest = Contest::find($params['contest_id']);
        $content = View::make('usercontext.contests.view')->with('contest', $contest);
        $this->response->addContent($content);
        break;
      default:
        return $this->response->addContent("/* No handler for route {$route_name} */");
    }

    return $this->response;
  }
  
  function contest_listing()
  {
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

    $content = View::make('usercontext.contests.list')->with('recs', $recs);
    $this->response->addContent($content);
  }
}
