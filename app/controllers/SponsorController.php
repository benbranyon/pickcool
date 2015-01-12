<?php

class SponsorController extends BaseController
{

	public function create()
    {
	    $rules = array(
	        'name' => array('required'),
	        'url'       => array('required'),
	        'description' => array('required')
	    );

	    $validation = Validator::make(Input::all(), $rules);

	    if ($validation->fails())
	    {
	        // Validation has failed.
	        return Redirect::to('sponsor/signup/' . Input::get('contest_id'))->withErrors($validation);
	    }

		$sponsor = new Sponsor;
		$sponsor->name = Input::get('name');
		$sponsor->description = Input::get('description');
		$sponsor->url = Input::get('url');
		$fb_image_id = Input::get('image_id');

		$fb = \OAuth::consumer( 'Facebook' );
		try {
			$fb_image = json_decode( $fb->request( $fb_image_id . '?type=normal' ), true );
		}
		catch (Exception $e) {
		}
		$i = \Image::from_url($fb_image['source'],true);
		$sponsor->image_id = $i->id;
		$sponsor->save();
		$redirect = '/est/' . Input::get('contest_id') . '/' . Input::get('contest_slug');
		Session::put('success', 'Thank you for signing up to be a sponsor. A representative will contact you soon to confirm.');
	    return Redirect::to($redirect);
    }

    public function edit()
    {
    	
    }

}