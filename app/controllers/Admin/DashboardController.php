<?php namespace Admin;
use User;
use Vote;
use Image;

class DashboardController extends \BaseController {

	function index() {
		$users = User::count();
		$votes = Vote::count();
		$new_users = User::whereRaw('created_at > utc_timestamp() - interval 1 day')->count();
		$new_votes = Vote::whereRaw('voted_at > utc_timestamp() - interval 1 day')->count();

		$data = array(
			'users' => $users,
			'new_users' => $new_users,
      'votes'=>$votes,
      'new_votes'=>$new_votes,
		);
		return \View::make('admin.dashboard')->with('data', $data);
	}

	function upload_image() {
      $file = \Input::file('image');
      if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
      {
        \Session::put('danger', 'Please upload an image file before continuing.');
      } else {
        try
        {
          $image = new Image();
          $image->image = $file;
          $image->save();
          \Session::put('success', 'Image uploaded.');
          return \Redirect::to('/admin');
        } catch (Exception $e)
        {
          \Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
        }
      }

      return \Redirect::to('/admin');
	}
}