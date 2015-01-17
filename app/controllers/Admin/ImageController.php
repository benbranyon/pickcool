<?php namespace Admin;
use Image;
use Input;

class ImageController extends \BaseController {

	function index() {
    if(Input::get('a'))
    {
      $image= Image::find(Input::get('a'));
      $image->approve();
    }
    if(Input::get('d'))
    {
      $image= Image::find(Input::get('d'));
      $image->decline();
    }
		$images = Image::whereNull('screened_at')->whereNotNull('candidate_id')->paginate(15);
		return \View::make('admin.images')->with(['images'=>$images]);
	}

	function edit($id) {
		if (\Request::isMethod('post'))
		{
			$candidate = Candidate::where('id', '=', $id)->firstOrFail();
			$candidate->name = \Input::get('name');
			$candidate->vote_boost = \Input::get('vote_boost');
			$candidate->charity_name = \Input::get('charity_name');
			$candidate->charity_url = \Input::get('charity_url');
			if(\Input::get('image_url'))
			{
				$i = \Image::from_url(\Input::get('image_url'),true);
				$candidate->image_id = $i->id;
			}
			$candidate->save();

			\Session::put('success', "Candidate Saved!");
			return \Redirect::to('admin/candidates');			
		}
		else
		{
			$candidate = Candidate::where('id', '=', $id)->firstOrFail();
			if($candidate)
			{
				return \View::make('admin.candidates-edit')->with('candidate', $candidate);
			}

		}
	}
}