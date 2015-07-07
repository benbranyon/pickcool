<?php namespace Admin;
use User;
use Vote;

class DashboardController extends \BaseController {

	function index() {
		$users = User::all();
		//$votes = Vote::all();
		$new_users = User::where('created_at', '>', \DB::raw('NOW() - INTERVAL 1 DAY'))->get();
		//$new_votes = Vote::where('updated_at', '>', \DB::raw('NOW() - INTERVAL 1 DAY'))->get();

		$data = array(
			'users' => $users,
			'new_users' => $new_users,
		);*/
		return \View::make('admin.dashboard');
	}
}