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

		    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
		        {{ Form::label('password', 'Password') }}
		        {{ Form::text('password', $contest->password, ['class' => 'form-control']) }}
		        {{ $errors->first('password', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('ends_at') ? ' has-error' : '' }}">
		        {{ Form::label('ends_at', 'End Date') }}
		        <input type="datetime" name="ends_at" value="<?php echo $contest->ends_at;?>">
		        {{ $errors->first('ends_at', '<p class="help-block">:message</p>') }}
		    </div>

		    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
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
		    
		    <div class="form-group{{ $errors->has('is_archived') ? ' has-error' : '' }}">
		        {{ Form::label('is_archived', 'Archived') }}
		        <input type="hidden" name="is_archived" value="0"/>
		        <input type="checkbox" name="is_archived" value="1" <?php echo($contest->is_archived ? 'checked' : '')?>/>
		        {{ $errors->first('is_archived', '<p class="help-block">:message</p>') }}
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