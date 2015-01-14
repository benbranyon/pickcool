<?php namespace Admin;
use Sponsor;

class SponsorController extends \BaseController {

	function index() {
		$sponsors = Sponsor::paginate(15);
		return \View::make('admin.sponsors')->with('sponsors', $sponsors);
	}
}