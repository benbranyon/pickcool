<?php namespace Admin;
use Candidate;

class CandidateController extends \BaseController {

	function index() {
		$candidates = Candidate::orderBy('updated_at', 'DESC')->paginate(15);
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
  
  function charity_boost($id) {
    $candidate = Candidate::find($id);
    $contest = $candidate->contest;
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    $badge = new Badge();
    $badge->name = 'charity';
    $badge->vote_weight = 25;
    $badge->contest_id = $contest->id;
    $badge->candidate_id = $candidate->id;
    $badge->save();  
    Session::put('success', "Ok, {$candidate->name} now has a charity badge");
    return \Redirect::to('admin/candidates');
  }
}