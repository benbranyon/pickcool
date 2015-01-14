<?php namespace Admin;
use Candidate;

class CandidateController extends \BaseController {

	function index() {
		$candidates = Candidate::paginate(15);
		return \View::make('admin.candidates')->with('candidates', $candidates);
	}

	function edit($id) {
		if (\Request::isMethod('post'))
		{
			$candidate = Candidate::where('id', '=', $id)->firstOrFail();
			$candidate->name = \Input::get('name');
			$candidate->vote_boost = \Input::get('vote_boost');
			$candidate->charity_name = \Input::get('charity_name');
			$candidate->charity_url = \Input::get('charity_url');
			$candidate->push();

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