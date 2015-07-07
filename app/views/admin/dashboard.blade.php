@extends('appadmin')

@section('head')

@stop

@section('content')
<h4>Marn'in</h4>

<h5>App Stats</h5>
<ul class="list-group">
	<li class="list-group-item">Total Users: {{{count($data['users'])}}}</li>
	<li class="list-group-item">New Users: {{{count($data['new_users'])}}}</li>
</ul>
@stop