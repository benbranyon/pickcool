@extends('app')

@section('head')
<title>{{{$candidate->name}}} in {{{$contest->title}}} | pick.cool</title>
<meta property="fb:app_id" content="1497159643900204"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote for {{{$candidate->name}}} in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$candidate->canonical_url}}}"/>
<meta property="og:description" content="{{{$contest->description}}}"/>
<meta property="og:image" content="/images/{{$candidate->image_id}}/facebook?_c={{microtime(true)}}"/>
@stop

@section('content')
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
        <a class="btn btn-warning btn-full" href="{{$candidate->unvote_url}}">Unvote</a>
      @else
        <div onclick="vote()" class="btn btn-warning btn-full" href="{{$candidate->vote_url}}">Vote</a>
      @endif
    @endif
    @if($contest->is_shareable)
      <a class="btn btn-warning btn-full" href="{{$candidate->share_url}}"><i class="fa fa-facebook"></i> Share</a>
    @endif
  </div>
  <script>
    function vote()
    {
      xmlhttp=new XMLHttpRequest();
      xmlhttp.open("GET",{{json_encode($candidate->vote_url)}},true);
      xmlhttp.send();
      var d = document.getElementById("candidate");
      d.className = d.className + " selected";
      FB.ui({
        method: 'share',
        href: {{json_encode($candidate->canonical_url)}},
      }, function(response){
        window.location = {{json_encode($contest->canonical_url)}};
      });
    }
  </script>

@stop
