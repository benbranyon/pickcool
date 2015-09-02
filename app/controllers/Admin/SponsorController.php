<?php namespace Admin;
use Sponsor;

class SponsorController extends \BaseController {

	function index() {
		$sponsors = Sponsor::paginate(15);
		return \View::make('admin.sponsors')->with('sponsors', $sponsors);
	}

	function add() {
		if (\Request::isMethod('post'))
		{
			$sponsor = new Sponsor;
			$sponsor->name = \Input::get('name');
			$sponsor->description = \Input::get('description');
			$sponsor->url = \Input::get('url');
			if(\Input::get('image_url'))
			{
				$i = \Image::from_url(\Input::get('image_url'),true);
				$sponsor->image_id = $i->id;
			}
			$sponsor->save();

			\Session::put('success', "Sponsor Saved!");
			return \Redirect::to('admin/sponsors');	
		}
		else
		{
			$today = \Carbon::now();
			$contests = \Contest::where('ends_at','>=',$today);
			return \View::make('admin.sponsors-add')->with('contests', $contests);
		}
	}

	function edit($id) {
		if (\Request::isMethod('post'))
		{
			$sponsor = Sponsor::where('id', '=', $id)->firstOrFail();
			$sponsor->name = \Input::get('name');
			$sponsor->description = \Input::get('description');
			$sponsor->url = \Input::get('url');
			if(\Input::get('image_url'))
			{
				$i = \Image::from_url(\Input::get('image_url'),true);
				$sponsor->image_id = $i->id;
			}
			$sponsor->push();

			\Session::put('success', "Sponsor Saved!");
			return \Redirect::to('admin/sponsors');			
		}
		else
		{
			$sponsor = Sponsor::where('id', '=', $id)->firstOrFail();
			if($sponsor)
			{
				return \View::make('admin.sponsors-edit')->with('sponsor', $sponsor);
			}

		}
	}
}