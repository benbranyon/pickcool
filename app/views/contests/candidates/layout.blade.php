@extends('app')

@section('head')
<title>{{{$candidate->name}}} in {{{$contest->title}}} | pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote for {{{$candidate->name}}} in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$candidate->canonical_url($contest)}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
@if($candidate->image_id)
  <meta property="og:image" content="{{{$candidate->image_url('facebook')}}}?_c={{microtime(true)}}"/>
@endif
@stop

@section('content')
  <div class="view" style="text-align: center; width:100%; margin-left: auto; margin-right: auto;">
    <div class="header">
      <h1>
        <a href="{{{$candidate->canonical_url}}}">
          {{{$candidate->name}}}
        </a>
      </h1>
      @if(isset($candidate->vote_count_boost))
        <h3>{{$candidate->vote_count_boost}} votes</h3>
      @else
        <h3>{{$candidate->vote_count_0}} votes</h3>
      @endif
      <h2><a href="{{{$contest->canonical_url}}}">{{$contest->title}}</a></h2>
      @if($contest->sponsors->count()>0)
        <?php $sponsor = $contest->random_sponsor; ?>
        <h2>Sponsored by: <a href="{{{$sponsor->url}}}" target="_self">{{{$sponsor->name}}}</a></h2>
      @endif
    </div>
    <div class="body">
      @yield('contests.candidates.content')
    </div>
  </div>
@stop
