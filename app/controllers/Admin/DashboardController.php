<?php namespace Admin;

class DashboardController extends \BaseController {

	function index() {
		return \View::make('admin.dashboard');
	}
}