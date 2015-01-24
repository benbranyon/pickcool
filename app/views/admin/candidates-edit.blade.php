@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Edit <?php echo $candidate->name;?> (<?php echo $candidate->contest->title;?>)</h1>

	{{ Form::open(array('action' => array('Admin\\CandidateController@edit', $candidate->id), 'files' => true)) }}
		<fieldset>

	    	<legend>Candidate Info</legend>
		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', $candidate->name, ['class' => 'form-control']) }}
		        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
		        {{ Form::label('image_url', 'Image URL') }}
		        <br />
		        @if(isset($candidate->image_id))
		        	<img src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
		        @endif
		        {{ Form::text('image_url', null, ['class' => 'form-control']) }}
		        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('vote_boost') ? ' has-error' : '' }}">
		        {{ Form::label('vote_boost', 'Vote Boost') }}
		        {{ Form::text('vote_boost', $candidate->vote_boost, ['class' => 'form-control']) }}
		        {{ $errors->first('vote_boost', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('charity_name') ? ' has-error' : '' }}">
		        {{ Form::label('charity_name', 'Charity Name') }}
		        {{ Form::text('charity_name', $candidate->charity_name, ['class' => 'form-control']) }}
		        {{ $errors->first('charity_name', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('charity_url') ? ' has-error' : '' }}">
		        {{ Form::label('charity_url', 'Charity URL') }}
		        {{ Form::text('charity_url', $candidate->charity_url, ['class' => 'form-control']) }}
		        {{ $errors->first('charity_url', '<p class="help-block">:message</p>') }}
		    </div>		    

	    </fieldset>
		{{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}
    {{ Form::close() }}

@stop