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

  public function live()
  {
    $contests = Contest::live()->with('candidates')->get();
    return View::make('home')->with(['contests'=>$contests, 'state'=>'live']);
  }

  function archived() {
    $contests = Contest::archived()->with('candidates')->get();
    return View::make('home')->with(['contests'=>$contests, 'state'=>'archived']);
  }
}
