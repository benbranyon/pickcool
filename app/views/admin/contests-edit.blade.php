@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Edit {{{$contest->title}}}</h1>

	{{ Form::open(array('action' => array('Admin\\ContestController@edit', $contest->id), 'files' => true)) }}
    	<fieldset>
	    	<legend>Contest Info</legend>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
		        {{ Form::label('title', 'Title') }}
		        {{ Form::text('title', $contest->title, ['class' => 'form-control']) }}
		        {{ $errors->first('title', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('callout') ? ' has-error' : '' }}">
		        {{ Form::label('callout', 'Callout (at top)') }}
		        {{ Form::textarea('callout', $contest->callout, ['class' => 'form-control']) }}
		        {{ $errors->first('callout', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
		        {{ Form::label('description', 'Description (at bottom)') }}
		        {{ Form::textarea('description', $contest->description, ['class' => 'form-control']) }}
		        {{ $errors->first('description', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('prizes') ? ' has-error' : '' }}">
		        {{ Form::label('prizes', 'Prizes') }}
		        {{ Form::textarea('prizes', $contest->prizes, ['class' => 'form-control']) }}
		        {{ $errors->first('prizes', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('rules') ? ' has-error' : '' }}">
		        {{ Form::label('rules', 'Rules') }}
		        {{ Form::textarea('rules', $contest->rules, ['class' => 'form-control']) }}
		        {{ $errors->first('rules', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
		        {{ Form::label('password', 'Password') }}
		        {{ Form::text('password', $contest->password, ['class' => 'form-control']) }}
		        {{ $errors->first('password', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('ends_at') ? ' has-error' : '' }}">
		        {{ Form::label('ends_at', 'End Date') }}
		        <input class="form-control" id="ends_at" type="datetime" name="ends_at" value="<?php echo $contest->ends_at;?>">
		        {{ $errors->first('ends_at', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
		        {{ Form::label('state', 'State') }}
		        {{ Form::select('state', [
		        	'' => "",
			        'AL'=>"Alabama",
	                'AK'=>"Alaska", 
	                'AZ'=>"Arizona", 
	                'AR'=>"Arkansas", 
	                'CA'=>"California", 
	                'CO'=>"Colorado", 
	                'CT'=>"Connecticut", 
	                'DE'=>"Delaware", 
	                'DC'=>"District Of Columbia", 
	                'FL'=>"Florida", 
	                'GA'=>"Georgia", 
	                'HI'=>"Hawaii", 
	                'ID'=>"Idaho", 
	                'IL'=>"Illinois", 
	                'IN'=>"Indiana", 
	                'IA'=>"Iowa", 
	                'KS'=>"Kansas", 
	                'KY'=>"Kentucky", 
	                'LA'=>"Louisiana", 
	                'ME'=>"Maine", 
	                'MD'=>"Maryland", 
	                'MA'=>"Massachusetts", 
	                'MI'=>"Michigan", 
	                'MN'=>"Minnesota", 
	                'MS'=>"Mississippi", 
	                'MO'=>"Missouri", 
	                'MT'=>"Montana",
	                'NE'=>"Nebraska",
	                'NV'=>"Nevada",
	                'NH'=>"New Hampshire",
	                'NJ'=>"New Jersey",
	                'NM'=>"New Mexico",
	                'NY'=>"New York",
	                'NC'=>"North Carolina",
	                'ND'=>"North Dakota",
	                'OH'=>"Ohio", 
	                'OK'=>"Oklahoma", 
	                'OR'=>"Oregon", 
	                'PA'=>"Pennsylvania", 
	                'RI'=>"Rhode Island", 
	                'SC'=>"South Carolina", 
	                'SD'=>"South Dakota",
	                'TN'=>"Tennessee", 
	                'TX'=>"Texas", 
	                'UT'=>"Utah", 
	                'VT'=>"Vermont", 
	                'VA'=>"Virginia", 
	                'WA'=>"Washington", 
	                'WV'=>"West Virginia", 
	                'WI'=>"Wisconsin", 
	                'WY'=>"Wyoming"
		        ], $contest->state) }}
		        {{ $errors->first('state', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
		       	{{ Form::label('category_id', 'Category') }}
		        {{ Form::select('category_id', [
		        	'0' => "",
			        '1'=>"Bands",
			        '2'=>"Other",
		        ], $contest->category_id) }}
		        {{ $errors->first('category_id', '<p class="help-block">:message</p>') }}
		    </div>
		    
		    <div class="form-group{{ $errors->has('is_archived') ? ' has-error' : '' }}">
		        {{ Form::label('is_archived', 'Archived') }}
		        <input type="hidden" name="is_archived" value="0"/>
		        <input type="checkbox" name="is_archived" value="1" <?php echo($contest->is_archived ? 'checked' : '')?>/>
		        {{ $errors->first('is_archived', '<p class="help-block">:message</p>') }}
		    </div>

	    </fieldset>

		{{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}
    {{ Form::close() }}

@stop