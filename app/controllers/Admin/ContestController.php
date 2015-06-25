<?php namespace Admin;
use Contest;
use Candidate;
use Image;

class ContestController extends \BaseController {

	function index() {
		$contests = Contest::paginate(15);
		return \View::make('admin.contests')->with('contests', $contests);
	}

	function edit($id) {
		if (\Request::isMethod('post'))
		{
			$contest = Contest::where('id', '=', $id)->firstOrFail();
			$contest->title = \Input::get('title');
			$contest->description = \Input::get('description');
			$contest->password = \Input::get('password');
			$contest->state = \Input::get('state');
			$contest->push();

			\Session::put('success', "Contest Saved!");
			return \Redirect::to('admin/contests');			
		}
		else
		{
			$contest = Contest::where('id', '=', $id)->firstOrFail();
			if($contest)
			{
				return \View::make('admin.contests-edit')->with('contest', $contest);
			}

		}
	}
}