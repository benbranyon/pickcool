@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Edit {{{$sponsor->name}}}</h1>

	{{ Form::open(array('action' => array('Admin\\SponsorController@edit', $sponsor->id), 'files' => true)) }}
		<fieldset>

	    	<legend>Sponsor Info</legend>
		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', $sponsor->name, ['class' => 'form-control']) }}
		        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('image_url', 'Image URL') }}
		        <br />
		        <img src="{{{$sponsor->image_url('mobile')}}}" alt="{{{$sponsor->name}}}" title="Vote for {{{$sponsor->name}}}"/>
		        {{ Form::text('image_url', null, ['class' => 'form-control']) }}
		        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('description', 'Description') }}
		        {{ Form::textarea('description', $sponsor->description, ['class' => 'form-control']) }}
		        {{ $errors->first('description', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('url', 'URL') }}
		        {{ Form::text('url', $sponsor->url, ['class' => 'form-control']) }}
		        {{ $errors->first('url', '<p class="help-block">:message</p>') }}
		    </div>

	    </fieldset>
		{{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}
    {{ Form::close() }}

@stop