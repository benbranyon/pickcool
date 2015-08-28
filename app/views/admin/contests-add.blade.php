@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Add contest</h1>

	{{ Form::open(array('action' => array('Admin\\ContestController@add'), 'files' => true)) }}

		{{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}

	{{ Form::close() }}


@stop