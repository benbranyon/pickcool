<?php

class CandidateImagesController extends \BaseController 
{
  function images($contest_id, $contest_slug, $candidate_id, $candidate_slug) {
    $contest = Contest::find($contest_id);
    $candidate = Candidate::find($candidate_id);
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
  
    return View::make('contests.candidates.images.list')->with(['contest'=>$contest, 'candidate'=>$candidate]);
  }

  function add($contest_id, $contest_slug, $candidate_id, $candidate_slug) 
  {
    $contest = Contest::find($contest_id);
    $candidate = Candidate::find($candidate_id);
    if(!$contest || !$candidate)
    {
      App::abort(404);
    }
    if($contest->is_ended)
    {
      Session::put('danger', "Sorry, voting has ended.");
      return Redirect::to($contest->canonical_url);
    }
  
    if(Request::isMethod('post'))
    {
      $file = Input::file('picture');
      if(!is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile'))
      {
        Session::put('danger', 'Please upload an image file before continuing.');
      } else {
        try
        {
          $image = new Image();
          $image->image = $file;
          $image->candidate_id = $candidate->id;
          $image->save();
          Session::put('success', 'Your image has been sumbitted and will be reviewed in the next 24-48 hours.');
          return Redirect::to($candidate->canonical_url);
        } catch (Exception $e) {
          Session::put('danger', 'We were unable to recognize that image file format. Please try uploading a different file.');
        }
      }
    }  
    return View::make('contests.candidates.images.add')->with(['contest'=>$contest, 'candidate'=>$candidate]);
  }

}
