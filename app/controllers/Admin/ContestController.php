<?php namespace Admin;
use Contest;
use Candidate;
use Image;

class ContestController extends \BaseController {

	function index() {
		$contests = Contest::query()->orderBy('is_archived', 'ASC')->orderBy('ends_at', 'DESC')->paginate(15);
		return \View::make('admin.contests')->with('contests', $contests);
	}

	function add() {
		if (\Request::isMethod('post'))
		{
			$contest = new Contest;
			$contest->title = \Input::get('title');
			$contest->description = \Input::get('description');
			$contest->prizes = \Input::get('prizes');
			$contest->rules = \Input::get('rules');
			$contest->password = \Input::get('password');
			$contest->state = \Input::get('state');
			$contest->is_archived = \Input::get('is_archived');
      		$contest->callout =  \Input::get('callout');
      		$contest->ends_at = \Input::get('ends_at');
      		$contest->category_id = \Input::get('category_id');
      		$contest->writein_enabled = \Input::get('writein_enabled');
			$contest->save();

			\Session::put('success', "Contest Saved!");
			return \Redirect::to('admin/contests');	
		}
		else
		{
			return \View::make('admin.contests-add');
		}
	}

	function edit($id) {
		if (\Request::isMethod('post'))
		{
			$contest = Contest::where('id', '=', $id)->firstOrFail();
			$contest->title = \Input::get('title');
			$contest->description = \Input::get('description');
			$contest->prizes = \Input::get('prizes');
			$contest->rules = \Input::get('rules');
			$contest->password = \Input::get('password');
			$contest->state = \Input::get('state');
			$contest->is_archived = \Input::get('is_archived');
      		$contest->callout =  \Input::get('callout');
      		$contest->ends_at = \Input::get('ends_at');
      		$contest->category_id = \Input::get('category_id');
      		$contest->writein_enabled = \Input::get('writein_enabled');
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