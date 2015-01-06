@extends('app')

@section('content')
<div class="container">
  <div class="sponsor">
    <h1>
      {{{$contest->title}}} Sponsor Signup
    </h1>

    {{ Form::open(array('action' => array('SponsorController@edit'), 'files' => true)) }}
    	<fieldset>
	    	<legend>Sponsor Info</legend>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', null, ['class' => 'form-control']) }}
		        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
		    	{{ Form::label('url', 'URL') }}
		    	{{ Form::text('url', null, ['class' => 'form-control']) }}
		    	{{ $errors->first('url', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
		        {{ Form::label('description', 'Description') }}
		        {{ Form::textarea('description', null, ['class' => 'form-control']) }}
		        {{ $errors->first('description', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
		        {{ Form::label('image', 'Image') }}
		    </div>

	    </fieldset>
		{{Form::submit('Signup', array('class' => 'btn btn-primary'))}}
    {{ Form::close() }}

   </div>
</div>

@stop