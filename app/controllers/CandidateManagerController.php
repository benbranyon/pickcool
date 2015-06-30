<?php

class CandidateManagerController extends \BaseController {
  function view($contest_id, $contest_slug, $candidate_id, $candidate_slug, $cmd_name, $cmd_id) {
    $contest = Contest::find($contest_id);
    if(!$contest)
    {
      App::abort(404);
    }
    if($contest->is_ended)
    {
      Session::put('danger', "Sorry, voting has ended.");
      return Redirect::to($contest->canonical_url);
    }
    if(!$contest->can_view)
    {
      Session::put('r', Request::url());
      return Redirect::to($contest->login_url);
    }
    $candidate = Candidate::find($candidate_id);
    if(!$candidate)
    {
      App::abort(404);
    }
    if(Auth::user() && Input::get('v'))
    {
      Auth::user()->vote_for($candidate);
    }
    if(!$candidate->is_active)
    {
      App::abort(404);
    }
    if($candidate->user_id != Auth::user()->id)
    {
      Session::put('danger', "You are not authorized to manage this candidate.");
      return Redirect::to($candidate->canonical_url);
    }
    $image = Image::find($cmd_id);
    if(!$image || !$image->candidate_id == $candidate->id)
    {
      Session::put('danger', "You are not authorized to manage this candidate.");
      return Redirect::to($candidate->canonical_url);
    }
    if($image->id == $candidate->image_id)
    {
      Session::put('danger', "You cannot manage the featured picture. Change the featured picture, then manage it.");
      return Redirect::to($candidate->canonical_url);
    }
  
    switch($cmd_name)
    {
      case 'featured':
        if($image->status != 'featured')
        {
          Session::put('danger', "You cannot feature this image.");
          return Redirect::to($candidate->canonical_url);
        }
        Session::put('success', 'Featured image updated.');
        $candidate->image_id = $image->id;
        $candidate->save();
        break;
      case 'delete':
        Session::put('success', 'Image deleted.');
        $image->delete();
        break;
      case 'moveup':
        $images = $candidate->weighted_images;
        $weight = 0;
        for($i=0;$i<count($images);$i++)
        {
          $images[$i]->weight = $weight++;
          if($images[$i]->id == $image->id)
          {
            if($i>0) $images[$i-1]->weight++;
            $images[$i]->weight--;
          }
        }
        foreach($images as $i) $i->save();
        break;
      case 'movedown':
        $images = $candidate->weighted_images;
        $weight = 0;
        for($i=0;$i<count($images);$i++)
        {
          $images[$i]->weight = $weight++;
          if($i>0 && $images[$i-1]->id == $image->id)
          {
            $images[$i-1]->weight++;
            $images[$i]->weight--;
          }
        }
        foreach($images as $i) $i->save();
        break;
      
    }
    return Redirect::to($candidate->canonical_url);
  }
}
