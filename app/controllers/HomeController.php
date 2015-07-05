<?php

function objectify($fields, $src)
{
  $obj = new stdclass();
  foreach($fields as $k=>$v)
  {
    if(is_numeric($k)) $k = $v;
    if(is_callable($v))
    {
      $obj->$k = $v($c);
    } else {
      $obj->$k = $src->$k;
    }
  }
  return $obj;
}
class HomeController extends \BaseController {

  public function hot()
  {
    $contests = Contest::hot()->with('candidates')->get();
    return View::make('home')->with(['contests'=>$contests, 'state'=>'hot']);
  }

  function top() {
    $contests = Contest::top()->with('candidates')->get();
    return View::make('home')->with(['contests'=>$contests, 'state'=>'top']);
  }
  
  function newest() {
    $contests = Contest::recent()->with('candidates')->get();
    return View::make('home')->with(['contests'=>$contests, 'state'=>'new']);
  }
}
