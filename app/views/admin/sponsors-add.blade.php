@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Add Sponsor</h1>
	{{ Form::open(array('action' => array('Admin\\SponsorController@add'), 'files' => true)) }}
		<fieldset>

	    	<legend>Sponsor Info</legend>
		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', '', ['class' => 'form-control']) }}
		        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('image_url') ? ' has-error' : '' }}">
		        {{ Form::label('image_url', 'Image URL') }}
		        <br />
		        {{ Form::text('image_url', null, ['class' => 'form-control']) }}
		        {{ $errors->first('image_url', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
		        {{ Form::label('description', 'Description') }}
		        {{ Form::textarea('description', '', ['class' => 'form-control']) }}
		        {{ $errors->first('description', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
		        {{ Form::label('url', 'URL') }}
		        {{ Form::text('url', '', ['class' => 'form-control']) }}
		        {{ $errors->first('url', '<p class="help-block">:message</p>') }}
		    </div>

	    </fieldset>
		{{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}
    {{ Form::close() }}

@stop