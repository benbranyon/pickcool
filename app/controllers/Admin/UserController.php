<?php namespace Admin;
use User;

class UserController extends \BaseController {

	function index() {
		$users = User::paginate(15);
		return \View::make('admin.users')->with('users', $users);
	}
}