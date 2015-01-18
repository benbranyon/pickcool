<?php namespace Admin;
use Image;
use Input;
use Carbon;
use Message;
use View;
use Redirect;

class ImageController extends \BaseController {

	function index() {
    if(Input::get('a'))
    {
      $image= Image::find(Input::get('a'));
      $image->approve();
    }
    if(Input::get('d'))
    {
      $image= Image::find(Input::get('d'));
      $image->decline();
    }
		$images = Image::whereNull('screened_at')->whereNotNull('candidate_id')->paginate(15);
		return \View::make('admin.images')->with(['images'=>$images]);
	}
  
  function set_status($image_id, $status)
  {
    $image = Image::find($image_id);
    $image->screened_at = Carbon::now();
    $image->status = $status;
    $image->save();
    
    switch($status)
    {
      case 'featured':
        if($image->candidate->image_id == null)
        {
          $image->candidate->image_id = $image->id;
          $image->candidate->save();
    
          $client = new \GuzzleHttp\Client();
          $client->post('https://graph.facebook.com/v2.2', ['query'=>[
            'access_token'=>'1497159643900204|DJ6wUZoCJWlOWDHegW1fFNK9r-M',
            'id'=>$image->candidate->canonical_url,
            'scrape'=>'true',
          ]]);
          Message::create([
            'user_id'=>$image->candidate->user_id,
            'subject'=>'Welcome to the pick!',
            'body'=>View::make('messages.join', [
              'candidate'=>$image->candidate,
              'contest'=>$image->candidate->contest,
            ]),
          ]);             
        } else {
          Message::create([
            'user_id'=>$image->candidate->user_id,
            'subject'=>'Image approved (featured)',
            'body'=>View::make('messages.image.approved-featured', ['image'=>$image]),
          ]);
        }
        break;
      case 'approved':
        Message::create([
          'user_id'=>$image->candidate->user_id,
          'subject'=>'Image approved (standard)',
          'body'=>View::make('messages.image.approved-standard', ['image'=>$image]),
        ]);
        break;
      case 'adult':
        Message::create([
          'user_id'=>$image->candidate->user_id,
          'subject'=>'Image approved (18+)',
          'body'=>View::make('messages.image.approved-adult', ['image'=>$image]),
        ]);
        break;
      case 'declined':
        if($image->candidate)
        {
          if($image->id == $image->candidate->image_id)
          {
            $image->candidate->image_id = null;
            $another = Image::whereCandidateId($this->candidate_id)->whereStatus('featured')->first();
            if($another)
            {
              $image->candidate->image_id = $another->id;
            }
            $image->candidate->save();
          }
        }
        Message::create([
          'user_id'=>$image->candidate->user_id,
          'subject'=>'Image returned',
          'body'=>View::make('messages.image.declined', ['image'=>$image]),
        ]);
    }
    
    return Redirect::to(r('admin.images'));
  }
}