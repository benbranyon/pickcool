<?php namespace Admin;
use Badge;

class BadgeController extends \BaseController {

	function index() {

		$badges = Badge::paginate(15);
		return \View::make('admin.badges')->with(['badges'=>$badges]);
	}

}