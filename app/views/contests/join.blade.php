@extends('app')

@section('head')
<title>Join {{{$contest->title}}} | pick.cool</title>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
<script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
@stop

@section('content')
  <div style="max-width: 320px; width: 100%; margin-left: auto;margin-right: auto">
    <h1>Join the Pick</h1>
    <h2>{{{$contest->title}}}</h2>
    @if($state=='begin')
      <p>You are about to enter the pick and receive votes.</p>
      <p>Proceed to entering this pick?</p>
      <p>
        <a href="{{{$contest->canonical_url}}}" class="btn btn-xl btn-danger"><i class="fa fa-arrow-left"></i> No</a>
        <a href="?s=rules" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, I want to Enter</a>
      </p>
    @endif
    @if($state=='rules')
      <p>Read and agree to the pick rules to make sure you are eligible.</p>
      <div style="max-height: 300px;overflow-y: auto;border: 1px solid rgb(231, 230, 230);padding: 5px;margin: 5px;border-radius: 3px;">
        <h2>PICK RULES</h2>
        {{Markdown::render($contest->rules)}}
      </div>
      <p>Are you eligible to enter this pick?</p>
      <p>
        <a href="{{{$contest->canonical_url}}}" class="btn btn-xl btn-danger"><i class="fa fa-arrow-left"></i> No</a>
        @if($contest->category->name == 'Bands')
          <a href="?s=bands" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, continue</a>
        @else
          <a href="?s=picture" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, continue</a>
        @endif
      </p>
    @endif
    @if($state=='picture')
      <h1>Picture Approval</h1>
      <p>Upload a picture to display for voting. Our content approval team will review it ASAP.
      {{Form::open(['url'=>Request::url()."?s=picture", 'files'=>true])}}
        Picture: {{Form::file('image', ['id'=>'picture'])}}<br/> {{Form::submit('Upload')}}
      {{Form::close()}}
      <h2>Picture Guidelines</h2>
      <p>        We want everyone to look their best, including the pick as a whole. Your voting profile will not be active until your picture is approved by our content review team. For faster approval, follow the guidelines below. Our content team may approve or decline a picture at its sole discretion. In general, we like thoughtful, artistic pictures and dislike cell phone or selfie pictures.</p>

      <p style="color: green">Do:</p>
      <ul>
        <li>Use a professional or semi-professional photograph
        <li>Make sure the image clearly depicts you and only you
        <li>Crop to a SQUARE shape for best results
      </ul>
      <p style="color: red">Don't</p>
      <ul>
        <li>No selfies (or pictures that might be confused with a selfie)
        <li>No pets, children, or other people in the shot
        <li>No grainy/blurry/poor contrast shots
        <li>No extreme angles 
        <li>Busy backgrounds or backgrounds featuring ordinary residential settings (couches, bathrooms, doors, etc).
      </ul>
    @endif
    @if($state=='bands')
      <h1>Band Signup</h1>
      {{Form::open(['url'=>Request::url()."?s=bands", 'files'=>true])}}
        <fieldset>
          <legend>Required</legend>
          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Name</label>
            {{ Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) }}
            {{ $errors->first('name', '<p class="help-block">:message</p>') }}
          </div>
          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label>Picture</label>
            {{Form::file('image', ['id'=>'picture'])}}
          </div> 
          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="music_url">Music URL</label>
            {{ Form::text('music_url', null, ['id' => 'name', 'class' => 'form-control']) }}
            <p class="help-block">Enter the url to a page where people can check out more of your music.</p>
          </div>
          
          {{ $errors->first('music_url', '<p class="help-block">:message</p>') }}
        <br />
        </fieldset>
        <fieldset>
          <legend>Extras</legend>
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
              <label>Bio</label>
              {{ Form::textarea('bio', null, ['id' => 'name', 'class'=>'form-control']) }}
            </div>
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
              <label>Youtube URL</label>
              {{ Form::text('youtube_url', null, ['id' => 'name', 'class'=>'form-control']) }}
              <p class="help-block">Enter the url to a youtube video you would like embeded on your profile page.</p>
            </div>
        </fieldset>
        {{Form::submit('Submit', array('class' => 'btn btn-md btn-primary'))}}
      {{Form::close()}}
      <h2>Music Guidelines</h2>
      <p> 
        Please enter the url to a page where voters can check out your music!
      </p>
    @endif
    @if($state=='preview')
      <div class="clearfix">
        <div style="float: left; text-align: center; border: 1px solid rgb(223, 223, 223); border-radius: 3px; margin-right: 3px;">
          <h2>{{{Auth::user()->full_name}}}</h2>
          <div class="candidate-small" >
            <img src="{{{$image->url('thumb')}}}" alt="{{{Auth::user()->full_name}}}" title="Vote for {{{Auth::user()->full_name}}}">
            <div class="clearfix">
              <div class="badges pull-right">
                <span class='badge badge-vote-count' title="420 votes">420</span>
              </div>
            </div>
          </div>
        </div>
        <p class="text-danger">This is really what they're going to vote on if you press Join. Seriously, if you don't like it, <a href="?s=picture">go back</a> and change it.</p>
      </div>
      <p>Proceed to entering this pick?</p>
      <p class="clearfix">
        <a href="{{{$contest->canonical_url}}}" class="btn btn-xl btn-danger"><i class="fa fa-arrow-left"></i> No</a>
        <a href="{{{r('contest.join', [$contest->id, 's'=>'finalize', 'img'=>$image->id])}}}" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, Join already!</a>
      </p>
    @endif
    @if($state=='done')
      <p>Congratulations, you are in the pick!</p>
      <p class="clearfix">
        <a href="{{{$candidate->canonical_url}}}" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> See Your Profile</a>
      </p>
    @endif
  </div>
  <script>
    if (navigator.userAgent.match(/FB/)) {
      $('#picture').attr('multiple',true);
      $('#picture').change(function(){
          if ($('#picture')[0].files.length > 1) {
            var control = $("#picture");
            control.replaceWith( control = control.clone( true ) );
            setTimeout(function() {
              alert("Please upload 1 file at a time.");
            },250)
          }
      });
    }    
  </script>
  
@stop
