@extends('appadmin')

@section('head')

@stop

@section('content')
<h4>Marn'in</h4>

<h5>App Stats</h5>
<ul class="list-group">
	<li class="list-group-item">Total Users: {{{$data['users']}}}</li>
	<li class="list-group-item">New Users: {{{$data['new_users']}}}</li>
	<li class="list-group-item">Total Votes: {{{$data['votes']}}}</li>
	<li class="list-group-item">New Votes: {{{$data['new_votes']}}}</li>
</ul>
@stop