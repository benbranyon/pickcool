<?php
namespace Api\V1;
  
use \BaseController;
use \Auth;
use \ApiSerializer;
use \Input;
use \Validator;
use \Contest;
use \Candidate;
use \Image;

class ContestEditController extends BaseController
{

  function api_add_edit_contest()
  {
  
    // If the user is not authenticated, bail
    if(!Auth::user())
    {
      return ApiSerializer::error(API_ERR_AUTH);
    }

    $has_error = false;
    $data = json_decode(Input::get('contest'),true);
    array_walk_recursive($data, function($v,$k) {
      $v = trim($v);
    });
  
    function init($rec, $name, $default=null) {
      return [
        'value'=>isset($rec[$name]['value']) ? $rec[$name]['value'] : $default,
        'errors'=>[],
      ];
    } 
    $res = [
      'id'=>init($data, 'id'),
      'title'=>init($data, 'title'),
    ];
  
    $validator = Validator::make(
      $data,
      [
        'title.value'=>'required',
      ],
      [
        'title.value.required'=>'Contest title is required',
      ]
    );
    if($validator->fails())
    {
      $has_error = true;
      $res['title']['errors'] = $validator->messages()->get('title.value');
    }
  
    // Validate candidates
    $res['candidates'] = [];
    foreach($data['candidates'] as $rec)
    {
      $candidate = [
        'id'=>init($rec, 'id'),
        'name'=>init($rec, 'name'),
        'image_url'=>init($rec, 'image_url'),
        'buy_url'=>init($rec, 'buy_url'),
        'buy_text'=>init($rec, 'buy_text'),
        'should_delete'=>init($rec, 'should_delete'),
      ];
      if(!$candidate['should_delete']['value'])
      {
        $validator = Validator::make(
          $rec,
          [
            'name.value'=>'required',
            'image_url.value'=>'required',
            'buy_url.value'=>'required',
            'buy_text.value'=>'required',
          ],
          [
            'name.value.required' => 'Candidate name is required',
            'image_url.value.required' => 'Candidate image URL is required',
            'buy_url.value.required' => 'Candidate buy URL is required',
            'buy_text.value.required' => 'Candidate buy text is required',
          ]
        );
        if($validator->fails())
        {
          $has_error = true;
          $m = $validator->messages();
          if($m->get('name.value')) $candidate['name']['errors'] = $m->get('name.value');
          if($m->get('image_url.value')) $candidate['image_url']['errors'] = $m->get('image_url.value');
          if($m->get('buy_url.value')) $candidate['buy_url']['errors'] = $m->get('buy_url.value');
          if($m->get('buy_text.value')) $candidate['buy_text']['errors'] = $m->get('buy_text.value');
        }
      }
    

      $res['candidates'][] = $candidate;
    
    }

    if( $has_error)
    {
      return ApiSerializer::error(API_ERR_VALIDATION, $res);
    }
  
    // Create or update
    // If an ID is provided, it means we're editing a contest
    // Make sure the user is allowed to edit
    if($res['id']['value'])
    {
      $contest = Contest::find($res['id']['value']);
      if(!$contest)
      {
        // If the record is not found, bail out
        return ApiSerializer::error(API_ERR_LOOKUP);
      }
      if(!$contest->is_editable_by(Auth::user()))
      {
        return ApiSerializer::error(API_ERR_AUTH);
      }
    } else {
      $contest = new Contest();
      $contest->user_id = Auth::user()->id; // Only assign user if creating
    }
    $contest->title = $res['title']['value'];
    $contest->save();
    $res['id']['value'] = $contest->id;
    // Update candidates

    foreach($res['candidates'] as $k=>$can)
    {
      if($can['id']['value'])
      {
        $c = Candidate::find($can['id']['value']);
        if(!$c)
        {
          return ApiSerializer::error(API_ERR_LOOKUP);
        }
        if(!$c->is_editable_by(Auth::user()))
        {
          return ApiSerializer::error(API_ERR_AUTH);
        }
        if($can['should_delete']['value'])
        {
          $c->delete();
          unset($can[$k]);
          continue;
        }
      } else {
        $c = new Candidate();
        $c->contest_id = $contest->id;
      }
      if($can['should_delete']['value']) continue;
      $i = Image::from_url($can['image_url']['value']);
      $c->name = $can['name']['value'];
      $c->image_id = $i->id;
      $c->buy_url = $can['buy_url']['value'];
      $c->buy_text = $can['buy_text']['value'];
      $c->save();
      $can['id']['value'] = $c->id;
    }
    return ApiSerializer::ok($contest);
  }
  
  
  function create() {
    return $this->api_add_edit_contest();
  }
  function save() {
    return $this->api_add_edit_contest();
  }
}