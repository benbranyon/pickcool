<?php
define('API_ERR_AUTH', 1);
define('API_ERR_VALIDATION', 2);
define('API_ERR_LOOKUP', 3);
  
class ApiSerializer
{
  static  $API_ERRORS=[
    API_ERR_AUTH=>'User not authorized to perform this operation.',
    API_ERR_VALIDATION=>'Data validation failed.',
    API_ERR_LOOKUP=>'Data not found',
  ];
  
  
  static function serialize($obj, $size='thumb')
  {
    if(is_object($obj))
    {
      $class = get_class($obj);
      Log::info($class);
      if($class=='Contest')
      {
        $v = null;
        if(Auth::check())
        {
          $v = Auth::user()->current_vote_for($obj);
        }
        $contest = [
          'id'=>(int)$obj->id,
          'title'=>$obj->title,
          'owner_id'=>(int)$obj->user_id,
          'current_user_candidate_id'=>(int)($v ? $v->candidate_id : null),
          'is_editable'=>$obj->is_editable_by(Auth::user()),
          'slug'=>$obj->slug(),
          'writein_enabled'=>$obj->writein_enabled == true,
          'ends_at'=>(int)($obj->ends_at ? $obj->ends_at->format('U') : null),
          'created_at'=>(int)$obj->created_at->format('U'),
          'candidates'=>$obj->candidates,
          'description'=>$obj->description,
          'sponsors'=>$obj->sponsors,
          'password'=>$obj->password,
          'vote_count'=>(int)$obj->vote_count + (count($obj->candidates)*2),
          'vote_count_hot'=>(int)$obj->vote_count_hot,
        ];
        return self::serialize($contest);
      }
      
      if($class=='Sponsor')
      {
        $sponsor = [
          'name'=>$obj->name,
          'description'=>$obj->description,
          'url'=>route('sponsor', [$obj->id]),
          'image_id'=>(int)$obj->image_id,
          'weight'=>(int)$obj->pivot->weight,
        ];
        return self::serialize($sponsor);
      }
      
      if($class=='Illuminate\Database\Eloquent\Collection')
      {
        $items = [];
        foreach($obj as $v) $items[] = $v;
        return self::serialize($items);
      }
      
      if($class=='Candidate')
      {
        $candidate = [
          'id'=>(int)$obj->id,
          'name'=>$obj->name,
          'image_id'=>(int)$obj->image_id,
          'vote_count'=>(int)$obj->total_votes+2,
          'fb_id'=>$obj->fb_id,
          'buy_text'=>$obj->buy_text,
          'canonical_url'=>route('contest.candidate.view', [$obj->contest_id, $obj->contest->slug(), $obj->id, $obj->slug()]),
          'current_rank'=>(int)$obj->current_rank,
        ];
        if($obj->is_editable_by(Auth::user()))
        {
          $candidate['original_image_url'] = $obj->image ? $obj->image->url : null;
          $candidate['original_buy_url'] = $obj->buy_url;
        }
        
        return self::serialize($candidate);
        
      }
    
      if($class=='User')
      {
        return self::serialize([
          'id'=>(int)$obj->id,
          'fb_id'=>$obj->fb_id,
          'name'=>$obj->full_name(),
          'first_name'=>$obj->first_name,
          'last_name'=>$obj->last_name,
          'email'=>$obj->email,
          'is_contributor'=>$obj->is_contributor == true,
        ]);
      }
          
      throw new Exception("API doesn't know how to serialize ".get_class($obj));
    }
    
    if(is_array($obj))
    {
      $new = [];
      foreach($obj as $k=>$v) $new[$k] = self::serialize($v,$size);
      $obj = $new;
    }
    
    return $obj;
  }
  
  static function error($errno, $data=null)
  {
    global $API_ERRORS;
    
    $payload = [
      'status'=> 'error', 
      'error_code'=>$errno,
      'error_message'=>self::$API_ERRORS[$errno],
      'data'=>self::serialize($data),
    ];
    return json_encode($payload);
  }
  
  static function ok($data=null)
  {
    $payload = [
      'status'=> 'ok', 
      'data'=>self::serialize($data),
    ];
    return json_encode($payload);
  }
}