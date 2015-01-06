@extends('app')

@section('head')
<title>{{{$candidate->name}}} in {{{$contest->title}}} | pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote for {{{$candidate->name}}} in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$candidate->canonical_url($contest)}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{$candidate->image_url('facebook')}}}?_c={{microtime(true)}}"/>
@stop

@section('content')
  <div class="view" style="text-align: center; max-width: 320px; width:100%; margin-left: auto; margin-right: auto;">
    <h1>
      @if($candidate->is_on_fire)
        <span class="fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
      @endif
      {{{$candidate->name}}}
    </h1>
    <h2><a href="{{{$contest->canonical_url}}}">{{$contest->title}}</a></h2>
    <h3>{{$candidate->vote_count_0}} votes</h3>
    <div id="candidate" class="candidate-large {{{$candidate->is_user_vote ? 'selected' : ''}}}">
      <img src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
    </div>
    @if($candidate->is_on_fire)
      <span class="fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
      {{{$candidate->name}}} is on fire because the vote count has increased by {{{Candidate::$on_fire_threshold*100}}}% or more in the last 24 hours. Congratulations!
    @endif
    
    @if($contest->is_voteable)
      @if(!$candidate->is_user_vote)
        <a class="btn btn-lg btn-primary btn-full" href="{{{$candidate->vote_url}}}"><i class="fa fa-check"></i> Vote</a>
      @endif
    @endif
    @if($contest->is_shareable && Auth::user())
      <a class="btn btn-lg btn-primary btn-full" onclick="share()"><i class="fa fa-facebook"></i> Share</a>
    @endif
    @if($contest->is_voteable)
      @if($candidate->is_user_vote)
        <a class="btn btn-lg btn-warning btn-full" href="{{{$candidate->unvote_url}}}">Unvote</a>
      @endif
    @endif
    <a href="{{{$candidate->image_url('large')}}}" class="btn btn-warning btn-full btn-lg"><i class="fa fa-camera"></i> View Large</a>
    @if($candidate->is_writein)
      <a class="btn btn-lg btn-warning btn-full" href="{{{$contest->join_url}}}"><i class="fa fa-facebook"></i> Refresh Your Picture</a>
    @endif
    <a class="btn btn-default btn-lg btn-full"  href="{{{$contest->canonical_url}}}"><i class="fa fa-arrow-left"></i> Back to Pick</a>
    @if(!Auth::user())
      <a href="{{{$candidate->login_url}}}">Is this you? Log in to edit.</a>
    @endif

  </div>
  <script>
    function share()
    {
      FB.ui({
        method: 'share',
        href: {{json_encode($candidate->canonical_url($contest))}},
      });
    }

    function vote()
    {
      var serialize = function(obj) {
        var str = [];
        for(var p in obj)
          if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
          }
        return '?'+str.join("&");
      };
      
      FB.login(function(response) {
        if (response.authResponse) {
          var d = document.getElementById("candidate");
          d.className = d.className + " selected";
          var xmlhttp=new XMLHttpRequest();
          xmlhttp.open("GET",{{json_encode($candidate->vote_url)}}+serialize({fb_access_token: response.authResponse.accessToken, fb_user_id: response.authResponse.userID}),true);
          xmlhttp.send();
          setTimeout(function() {
            alert("Thanks for voting. Support {{$candidate->name}} even more by sharing your vote with your friends.");
            share();
          }, 0); 
        } else {
           alert('some error');
           console.log('User cancelled login or did not fully authorize.');
         }
      });
    }
  </script>

@stop
