@extends('app')

@section('head')
<title>Join {{{$contest->title}}} | pick.cool</title>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
@stop

@section('content')
  <div style="max-width: 320px; width: 100%; margin-left: auto;margin-right: auto">
    <h1>Join the Pick</h1>
    <h2>{{{$contest->title}}}</h2>
    @if(Input::get('s',1)==1)
      <p>You are about to enter the pick and receive votes.</p>
      <p>Proceed to entering this pick?</p>
      <p>
        <a href="{{{$contest->canonical_url}}}" class="btn btn-xl btn-danger"><i class="fa fa-arrow-left"></i> No</a>
        <a href="?s=2" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, I want to Enter</a>
      </p>
    @endif
    @if(Input::get('s')==2)
      <p>Read and agree to the pick rules to make sure you are eligible.</p>
      <div style="max-height: 300px;
overflow-y: auto;
border: 1px solid rgb(231, 230, 230);
padding: 5px;
margin: 5px;
border-radius: 3px;">
        <h2>PICK RULES</h2>
        {{Markdown::render($contest->rules)}}
      </div>
      <p>Are you eligible to enter this pick?</p>
      <p>
        <a href="{{{$contest->canonical_url}}}" class="btn btn-xl btn-danger"><i class="fa fa-arrow-left"></i> No</a>
        <a href="?s=3" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, continue</a>
      </p>
    @endif
    @if(Input::get('s')==3)
      <div class="clearfix">
        <div style="float: left;
      text-align: center;
      border: 1px solid rgb(223, 223, 223);
      border-radius: 3px;
      margin-right: 3px;">
          <h2>{{{Auth::user()->full_name}}}</h2>
          <div class="candidate-small" >
            <img src="{{{Auth::user()->profile_image_url}}}" alt="{{{Auth::user()->full_name}}}" title="Vote for {{{Auth::user()->full_name}}}">
            <div class="clearfix">
              <div class="badges pull-right">
                <span class='badge badge-vote-count' title="420 votes">420</span>
              </div>
            </div>
          </div>
        </div>
        <p class="text-danger">This is really what they're going to see if you press Join. Seriously, if you don't like it, go change your Facebook profile.</p>
      </div>
      <p>Proceed to entering this pick?</p>
      <p class="clearfix">
        <a href="{{{$contest->canonical_url}}}" class="btn btn-xl btn-danger"><i class="fa fa-arrow-left"></i> No</a>
        <a href="?s=4" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> Yes, continue already!</a>
      </p>
    </div>
  @endif
  @if(Input::get('s')==4)
    <p>Congratulations, you are in the pick!</p>
    <p class="clearfix">
      <a href="{{{$candidate->canonical_url}}}" class="btn btn-xl btn-success"><i class="fa fa-arrow-right"></i> See Your Profile</a>
    </p>
  @endif

@stop
