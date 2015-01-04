@extends('app')

@section('head')
<title>{{{$candidate->name}}} in {{{$contest->title}}} | pick.cool</title>
<meta property="fb:app_id" content="1497159643900204"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote for {{{$candidate->name}}} in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$candidate->canonical_url}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{route('image.view', [$candidate->image_id, 'facebook'])}}}?_c={{microtime(true)}}"/>
@stop

@section('content')
  @if(Input::get('v'))
    <div class="alert alert-success">
      <p>Awesome sauce, you voted for {{{$candidate->name}}}. BUT it's not over! Without your help, other picks will win. Share this page with your friends.</p>
      <p><a class="btn btn-xs btn-primary" onclick="share()"><i class="fa fa-facebook"></i> Share Now</a></p>
    </div>
  @endif
  <div class="view" style="text-align: center; max-width: 320px; width:100%; margin-left: auto; margin-right: auto;">
    <h1>{{{$candidate->name}}}</h1>
    <h2><a href="{{{$contest->canonical_url}}}">{{$contest->title}}</a></h2>
    <h3>{{$candidate->vote_count}} votes</h3>
    <a id="candidate" href="/images/{{{$candidate->image_id}}}/mobile" target="_blank" class="candidate-large {{{$candidate->is_user_vote ? 'selected' : ''}}}">
      <img src="/images/{{{$candidate->image_id}}}/mobile" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
    </a>
    @if($candidate->is_editable)
      <a class="btn btn-success btn-full"  href="{{{$candidate->edit_url}}}">Edit</a>
    @endif
    @if($contest->is_voteable)
      @if($candidate->is_user_vote)
        <a class="btn btn-warning btn-full" href="{{{$candidate->unvote_url}}}">Unvote</a>
      @else
        @if(Auth::user())
          <div onclick="vote()" class="btn btn-warning btn-full">Vote Now and Share</div>
        @else
          <a class="btn btn-warning btn-full" onclick="vote()"><i class="fa fa-facebook"></i> Vote Now and Share</a>
        @endif
      @endif
    @endif
    @if($contest->is_shareable && Auth::user())
      <a class="btn btn-warning btn-full" onclick="share()"><i class="fa fa-facebook"></i> Share</a>
    @endif
    @if($candidate->is_writein)
      <a class="btn btn-warning btn-full" href="{{{$contest->join_url}}}"><i class="fa fa-facebook"></i> Refresh Your Picture</a>
    @endif
    @if(!Auth::user())
      <a href="{{{$candidate->login_url}}}">Is this you? Log in to edit.</a>
    @endif

  </div>
  <script>
    function share(response)
    {
      FB.ui({
        method: 'share',
        href: {{json_encode($candidate->canonical_url)}},
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
