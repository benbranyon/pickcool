@extends('app')

@section('content')
  <div style="width: 600px; margin-left: auto; margin-right: auto;" class="clearfix message">
    <h1>{{{$message->subject}}}</h1>
    <div class="date">{{{$message->updated_at->diffForHumans()}}}</div>
    <div class="body">
      {{$message->body}}
    </div>
  </div>
@stop