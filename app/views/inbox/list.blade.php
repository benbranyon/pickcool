@extends('app')

@section('content')
  <div style="width: 600px; margin-left: auto; margin-right: auto;" class="clearfix">
    @foreach($messages as $m)
      <div class="message {{{$m->read_at ? 'read' : 'unread'}}}">
        <h1><a href="{{{route('inbox.read', [$m->id])}}}">{{{$m->subject}}}</a></h1>
        <div class="date">
          {{{$m->created_at->diffForHumans()}}}
        </div>
      </div>
    @endforeach
  </div>
@stop