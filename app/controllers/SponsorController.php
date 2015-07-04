<?php

class SponsorController extends BaseController
{
  
  function edit($id) {    return "hello";
  }
  
  function create($id) {
    $contest = Contest::find($id);
    if(!$contest)
    {
      App::abort(404);
    }
    $fb = \OAuth::consumer( 'Facebook' );
    //$has_token = $fb->getStorage()->hasAccessToken("Facebook");

    try
    {
      $fb_token = $fb->getStorage()->retrieveAccessToken("Facebook"); 
      $access_token = $fb_token->getAccessToken();
      $client = new \GuzzleHttp\Client();
      $fb_permissions =  $client->get('https://graph.facebook.com/me/permissions?access_token=' . $access_token); 
      $fb_permissions = $fb_permissions->json();
    }
    catch (Exception $e) {
      //Old token
      //$fb->getStorage()->clearToken("Facebook");
    }

    $user_photos = false;
    foreach($fb_permissions['data'] as $permission)
    {
      if(array_search('user_photos', $permission))
      {
        $user_photos = true;
      }
    }
    if(!$user_photos)
    {
      $dialog = 'https://www.facebook.com/dialog/oauth?client_id='. $_ENV['FACEBOOK_APP_ID']. '&redirect_uri=' .Request::root() .'/sponsor/signup/'.$id.'&scope=user_photos';
      return Redirect::to( (string) $dialog);
    }
    return View::make('sponsors.signup')->with(['contest'=>$contest]);
  }
    

	public function signup()
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


}