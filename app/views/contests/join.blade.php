@extends('app')

@section('head')
<title>Join {{{$contest->title}}} | pick.cool</title>
<meta property="fb:app_id" content="1497159643900204"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Join {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="/images/{{$contest->image_id}}/facebook?_c={{microtime(true)}}"/>


@stop

@section('content')
<div style="max-width: 320px; width: 100%; margin-left: auto;margin-right: auto">
  <h1>Join the Pick</h1>
  <h2>You are joining {{{$contest->title}}}</h2>
  <div style="float: left;
text-align: center;
border: 1px solid rgb(223, 223, 223);
border-radius: 3px;
margin-right: 3px;">
    <h2>{{{Auth::user()->full_name}}}</h2>
    <div class="candidate-small" >
      <img src="{{{Auth::user()->profile_img_url}}}" />
      <span class='votes-count'>420</span>
    </div>
  </div>
  <p class="text-danger">This is really what they're going to see if you press Join. Seriously, if you don't like it, go change your Facebook profile.</p>
  <p>
    So you want to join the pick, eh? Well, it's pretty easy. Go change your Facebook profile to whatever you feel makes your **<em>BEST</em>** impression. If you don't like what you see below, go update your Facebook profile and then come back to join the pick.
  </p>
  <a class="btn btn-primary" onclick="join()">Join Now & Share</a>
</div>
<script>
  function share(response)
  {
    FB.ui({
      method: 'share',
      href: {{json_encode($candidate->canonical_url)}},
    }, response);
  }
  function join()
  {
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function()
    {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
        share(function(response){
          if(!response.error_code)
          {
            window.location = {{json_encode($contest->canonical_url)}};
          }
        });
      }
    }    
    xmlhttp.open("GET",{{json_encode($contest->join_url)}},true);
    xmlhttp.send();
  }
</script>

@stop
