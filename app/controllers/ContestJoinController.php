<?php

class ContestJoinController extends \BaseController {

  function join($id) {
    $contest = Contest::find($id);
    if(!$contest)
    {
      App::abort(404);
    }
    if($contest->is_ended)
    {
      Session::put('danger', "Sorry, voting has ended.");
      return Redirect::to($contest->canonical_url);
    }
    $state = Input::get('s','begin');
    if(!$contest->is_joinable)
    {
      Session::put('danger', "You can not join {$contest->title}.");
      return Redirect::to($contest->canonical_url);
    }
    $with = [
      'contest'=>$contest,
      'state'=>$state,
    ];
    $image_id = Input::get('img');
    if($image_id)
    {
      $image = Image::find($image_id);
      if(!$image)
      {
        Session::put('warning', 'Please upload an image.');
        return Redirect::to(r('contest.join', [$contest->id, 's'=>'picture']));
      }
      $with['image']=$image;
    }
    $candidate_id = Input::get('c');
    if($candidate_id)
    {
      $candidate = Candidate::find($candidate_id);
      if(!$candidate)
      {
        Session::put('warning', 'An unexpected error ocurred.');
        return Redirect::to(r('contest.join'));
      }
      $with['candidate']=$candidate;
    }  
  
    if($state=='picture' && Request::isMethod('post'))
    {
      $file = Input::file('image');
      if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
      {
        Session::put('danger', 'Please upload an image file before continuing.');
      } else {
        try
        {
          $image = new Image();
          $image->image = $file;
          $image->save();
          return Redirect::to(r('contest.join', [$contest->id, 's'=>'preview', 'img'=>$image->id]));
        } catch (Exception $e)
        {
          Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
        }
      }
    }
    if($state == 'bands')
    {
      if (Request::isMethod('post'))
      {
        $rules = array(
            'name' => array('required'),
            'music_url'       => array('required', 'url')
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            // Validation has failed.
            Session::put('danger', 'Please enter your name, image, and valid music url including the http://.');
            return Redirect::to(r('contest.join', [$contest->id, 's'=>'bands']))->withErrors($validation);
        }
        $file = Input::file('image');
        $name = Input::get('name');
        $music_url = Input::get('music_url');
        $bio = Input::get('bio');
        $youtube_url = Input::get('youtube_url');
        if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
        {
          Session::put('danger', 'Please upload an image file before continuing.');
        } else {
          try
          {
            $image = new Image();
            $image->image = $file;
            $image->save();

          } catch (Exception $e)
          {
            Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
          }
            $user = Auth::user();
            if(!$user) return null;
            $can = new Candidate();
            $can->contest_id = $contest->id;
            $can->user_id = $user->id;
            $can->name = $name;
            $can->music_url = $music_url;
            $can->youtube_url = $youtube_url;
            $can->bio = $bio;
            $can->save();
            $can = Candidate::find($can->id);
            Message::create([
              'user_id'=>$user->id,
              'subject'=>"Pick joined - pending verification",
              'body'=>View::make('messages.join_verify')->with([
                'candidate'=>$can,
                'contest'=>$can->contest,
              ]),
            ]);
            $user->vote_for($can);

            $image->candidate_id = $can->id;
            $image->save();
            return Redirect::to($can->canonical_url);
        }
      }
    }
    if($state=='finalize')
    {
      $candidate = $contest->add_user();
      $image->candidate_id = $candidate->id;
      $image->save();

      Message::create([
        'user_id'=>Auth::user()->id,
        'subject'=>"Image review in progress",
        'body'=>View::make('messages.image.in_review')->with(['image'=>$image]),
      ]);
      return Redirect::to($candidate->canonical_url);
    }
    if($state=='done')
    {
      $with['candidate'] = $candidate;
    }
    return View::make('contests.join')->with($with);
  }

  function done($id) {
    $candidate = Candidate::find($id);
    $contest = $candidate->contest;
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    if($contest->is_ended)
    {
      Session::put('danger', "Sorry, voting has ended.");
      return Redirect::to($contest->canonical_url);
    }  
    return View::make('contests.after_join')->with(['candidate'=>$candidate, 'contest'=>$contest]);
  }  
  
}
