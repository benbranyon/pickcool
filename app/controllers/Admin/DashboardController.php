<?php namespace Admin;
use User;
use Vote;

class DashboardController extends \BaseController {

	function index() {
		$users = User::count();
		$votes = Vote::count();
		$new_users = User::whereRaw('created_at > utc_timestamp() - interval 1 day')->count();
		$new_votes = Vote::whereRaw('updated_at > utc_timestamp() - interval 1 day')->count();

		$data = array(
			'users' => $users,
			'new_users' => $new_users,
      'votes'=>$votes,
      'new_votes'=>$new_votes,
		);
		return \View::make('admin.dashboard')->with('data', $data);
	}
}