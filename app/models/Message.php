<?php
  
class Message extends Eloquent
{
  public function getDates()
  {
    return [
      'read_at',
      'created_at',
      'updated_at',
    ];
  }

  protected $fillable = ['user_id', 'subject', 'body'];

}