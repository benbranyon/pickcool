@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Edit <?php echo $contest->title;?></h1>

	{{ Form::open(array('action' => array('Admin\\ContestController@edit', $contest->id), 'files' => true)) }}
    	<fieldset>
	    	<legend>Contest Info</legend>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
		        {{ Form::label('title', 'Title') }}
		        {{ Form::text('title', $contest->title, ['class' => 'form-control']) }}
		        {{ $errors->first('title', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
		        {{ Form::label('description', 'Description') }}
		        {{ Form::textarea('description', $contest->description, ['class' => 'form-control']) }}
		        {{ $errors->first('description', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
		        {{ Form::label('password', 'Password') }}
		        {{ Form::text('password', $contest->password, ['class' => 'form-control']) }}
		        {{ $errors->first('password', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
		        {{ Form::label('ends_at', 'End Date') }}
		        <input type="datetime" name="ends_at" value="<?php echo $contest->ends_at;?>">
		        {{ $errors->first('ends_at', '<p class="help-block">:message</p>') }}
		    </div>

	    </fieldset>
	    <fieldset>
	    	<legend>Candidate Info</legend>

	    
	    	<?php $count = 0;?>
	    	@foreach($contest->candidates as $candidate)
	    		<?php echo $candidate->name;?>
	    		<img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}" alt="{{{$candidate->name}}}" title="{{{$candidate->name}}}">
			    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
			        {{ Form::label('candidates[name]', 'Name') }}
			        {{ Form::text('candidates[name]', $candidate->name, ['class' => 'form-control']) }}
			        {{ $errors->first('candidates[name]', '<p class="help-block">:message</p>') }}
			    </div>	
			    <div class="form-group{{ $errors->has('image_url') ? ' has-error' : '' }}">
			        {{ Form::label('candidates[image_url]', 'Image URL') }}
			        {{ Form::text('candidates[image_url]', null, ['class' => 'form-control']) }}
			        {{ $errors->first('candidates[image_url]', '<p class="help-block">:message</p>') }}
			    </div>
			    <div class="form-group{{ $errors->has('charity_name') ? ' has-error' : '' }}">
			        {{ Form::label('candidates[charity_name]', 'Charity Name') }}
			        {{ Form::text('candidates[charity_name]', $candidate->charity_name, ['class' => 'form-control']) }}
			        {{ $errors->first('candidates[charity_name]', '<p class="help-block">:message</p>') }}
			    </div>	
			    <div class="form-group{{ $errors->has('charity_url') ? ' has-error' : '' }}">
			        {{ Form::label('candidates[charity_url]', 'Charity URL') }}
			        {{ Form::text('candidates[charity_url]', $candidate->charity_url, ['class' => 'form-control']) }}
			        {{ $errors->first('candidates[charity_url]', '<p class="help-block">:message</p>') }}
			    </div>	
			    <div class="form-group{{ $errors->has('vote_boost') ? ' has-error' : '' }}">
			        {{ Form::label('candidates[vote_boost]', 'Vote Boost') }}
			        {{ Form::text('candidates[vote_boost]', $candidate->vote_boost, ['class' => 'form-control']) }}
			        {{ $errors->first('candidate[vote_boost]', '<p class="help-block">:message</p>') }}
			    </div>
			    <hr />	
			    <?php $count++;?>
	    	@endforeach
	    </fieldset>

		{{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}
    {{ Form::close() }}

@stop