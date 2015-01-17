@extends('appadmin')

@section('head')

@stop

@section('content')
<h4>Marn'in</h4>

<h5>App Stats</h5>
<ul class="list-group">
	<li class="list-group-item">Total Users: {{{count($data['users'])}}}</li>
	<li class="list-group-item">New Users: {{{count($data['new_users'])}}}</li>
	<li class="list-group-item">Total Votes: {{{count($data['votes'])}}}</li>
	<li class="list-group-item">New Votes: {{{count($data['new_votes'])}}}</li>
</ul>
@stop