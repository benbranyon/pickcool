<?php namespace Admin;
use User;
use Vote;

class DashboardController extends \BaseController {

	function index() {
		$users = User::all();
		$votes = Vote::all();
		$new_users = User::where('created_at', '>=', new \DateTime('today'));
		$new_votes = Vote::where('created_at', '>=', new \DateTime('today'));
		$data = array(
			'users' => $users,
			'new_users' => $new_users,
			'votes' => $votes,
			'new_votes' => $new_votes
		);
		return \View::make('admin.dashboard')->with('data', $data);
	}
}