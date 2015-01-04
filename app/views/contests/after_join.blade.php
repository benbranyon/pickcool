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
  <div style="max-width: 320px; width=100%; margin-left: auto; margin-right: auto">
    <div class="row">
      <div class="col-xs-4">
        <div class="candidate-small">
          <img src="/images/{{{$candidate->image_id}}}/mobile" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
        </div>
      </div>
      <div class="col-xs-8 text-success" style="font-size: 30px;">
        <i class="fa fa-check"></i> You Joined
      </div>
    </div>
        
    <div class="alert alert-success">
      <p>Awesome sauce, you joined the pick. But seriously, picks are won by people who share. So share this page with your friends.</p>
    </div>
    <button class="btn btn-primary btn-lg btn-full" onclick="share()"><i class="fa fa-facebook"></i> Share Now</button>
    
    <a class="btn btn-default btn-lg btn-full"  href="{{{$candidate->canonical_url}}}"><i class="fa fa-arrow-left"></i> Back to {{{$candidate->name}}}</a>
    <a class="btn btn-default btn-lg btn-full"  href="{{{$contest->canonical_url}}}"><i class="fa fa-arrow-left"></i> Back to Pick</a>
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
