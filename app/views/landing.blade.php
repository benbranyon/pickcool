@extends('app')

@section('head')
<title>pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="pick.cool"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{r('home')}}}"/>
<meta property="og:description" content="Vote and watch social contests in real time."/>
@if(isset($contest->current_winner))
  <meta property="og:image" content="{{{$contests[0]->current_winner->image_url('facebook')}}}" />
@endif
@stop

@section('content')
	<h1>Latest Pick Cool Events</h1>
	<h2>Musicians, Models, and Ink</h2>
	<h3 style="margin-bottom:10px;">Feb 19th 2015</h3>
	<a href="http://www.janugget.com/"><img style="max-width:150px;" alt="John Ascuaga's Nugget" class="img-responsive" src="/assets/img/nugget-color-logo.jpg" /></a>
	<hr />
	<div id="fb-root"></div><script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script><div class="fb-post" data-href="https://www.facebook.com/media/set/?set=a.339808079550788.1073741831.310629329135330&amp;type=1" data-width="320"><div class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/media/set/?set=a.339808079550788.1073741831.310629329135330&amp;type=1">Post</a> by <a href="https://www.facebook.com/the.pick.cool">Pick.Cool</a>.</div></div>
	<br />
@stop