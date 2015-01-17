<?php
  
class Image extends BenAllfree\LaravelStaplerImages\Image
{
  public function getDates()
  {
    return [
      'image_updated_at',
      'created_at',
      'updated_at',
      'screened_at',
    ];
  }
  

  
  function approve()
  {
    $this->screened_at = Carbon::now();
    $this->status = 'approved';
    $this->save();
    $this->candidate->image_id = $this->id;
    $this->candidate->save();
    
    $client = new \GuzzleHttp\Client();
    $client->post('https://graph.facebook.com/v2.2', ['query'=>[
      'access_token'=>'1497159643900204|DJ6wUZoCJWlOWDHegW1fFNK9r-M',
      'id'=>$this->candidate->canonical_url,
      'scrape'=>'true',
    ]]);
    
    Message::create([
      'user_id'=>$this->candidate->user_id,
      'subject'=>'Image approved',
      'body'=>View::make('admin.images.approve', ['image'=>$this]),
    ]);
    
  }
  
  function decline()
  {
    $this->screened_at = Carbon::now();
    $this->status = 'declined';
    $this->save();
    if($this->id == $this->candidate->image_id)
    {
      $this->candidate->image_id = null;
      $this->candidate->save();
    }
    Message::create([
      'user_id'=>$this->candidate->user_id,
      'subject'=>'Image returned',
      'body'=>View::make('admin.images.decline', ['image'=>$this]),
    ]);
  }
  
  function candidate()
  {
    return $this->belongsTo('Candidate');
  }
  
  function getContestAttribute()
  {
    return $this->candidate->contest;
  }
}