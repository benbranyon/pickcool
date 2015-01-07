@extends('app')

@section('head')
<title>Sponsor Signup {{{$contest->title}}} | pick.cool</title>
<script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="/assets/js/facebookphotoselector.jquery.js"></script>
<style type="text/css">
    .thumbnail.selected {
        background-color: #428bca;
    }
    .loading {
        text-align: center;
    }
    .modal-body {
        max-height: 600px;
        overflow-y: auto;
    }
</style>
@stop

@section('content')
<div class="container">
  <div class="sponsor">
    <h1>
      {{{$contest->title}}} Sponsor Signup
    </h1>

    {{ Form::open(array('action' => array('SponsorController@create'), 'files' => true)) }}
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
				{{ Form::hidden('contest_id', $contest->id) }}
				{{ Form::hidden('contest_slug', $contest->slug) }}
		    </div>

		    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
		        {{ Form::label('image_id', 'Image') }}
		        {{ Form::hidden('image_id', null) }}
		        <br />
		        <a class="btn btn-md btn-primary" data-toggle="modal" href="#facebook_photo_selector">Select Facebook Photo</a>
		        {{ $errors->first('image_id', '<p class="help-block">:message</p>') }}
		    </div>

	    </fieldset>
		{{Form::submit('Signup', array('class' => 'btn btn-md btn-primary'))}}
    {{ Form::close() }}

   </div>

	<!-- Facebook photo selector modal -->
	<div id="facebook_photo_selector" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Choose a photo</h4>
				</div>
				<div class="modal-body">
					<div class="form">
						<label>Select Facebook Album:</label>
						<select class="fbps-albums" name="facebook_photo_album"></select>
					</div>
					<hr>
					<div class="fbps-photos clearfix"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default fbps-cancel" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary fbps-select" data-dismiss="modal">Select Photo</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

</div>
	<script>
		$(function()
		{

			$(document).on('fbload', function(){
				FB.api(
				    "/me/permissions",
				    function (response) {
				      if (response && !response.error) {
				        /* handle the result */
						FB.login(function(response) {
						   // handle the response
						 }, {
						   scope: 'user_photos', 
						   return_scopes: true
						 });
				      }
				    }
				);
				FacebookPhotoSelector.setFacebookSDK(FB);
			});

			$('#facebook_photo_selector').facebookPhotoSelector({
				onFinalSelect : function(photos)
				{
					$('#image_id').val(photos);
				}
			});
		});
	</script>

@stop