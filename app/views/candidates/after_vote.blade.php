@extends('app')

@section('head')
<title>{{{$candidate->name}}} in {{{$contest->title}}} | pick.cool</title>
<meta property="fb:app_id" content="1497159643900204"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote for {{{$candidate->name}}} in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$candidate->canonical_url($contest)}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{$candidate->image_url('facebook')}}}?_c={{microtime(true)}}"/>
@stop

@section('content')
  <div style="max-width: 320px; width=100%; margin-left: auto; margin-right: auto">
    <div class="row">
      <div class="col-xs-4">
        <div class="candidate-small">
          <img src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
        </div>
      </div>
      <div class="col-xs-8 text-success" style="font-size: 30px;">
        <i class="fa fa-check"></i>
        @if(Input::get('v')=='new')
          You voted for {{$candidate->name}}
        @else
          @if(Input::get('v')=='changed')
            You changed your vote to {{$candidate->name}}
          @else
            You already voted for {{$candidate->name}}
          @endif
        @endif
      </div>
    </div>
        
    <div class="alert alert-success">
      <p>Awesome sauce, you voted for {{{$candidate->name}}}.</p>
      <p>You can only vote for ONE person, but you can change your vote any time.</p>
      <p>To help even more, share this page with your friends.</p>
    </div>
    <button class="btn btn-primary btn-lg btn-full" onclick="share()"><i class="fa fa-facebook"></i> Share Now</button>
    
    <a class="btn btn-default btn-lg btn-full"  href="{{{$candidate->canonical_url($contest)}}}"><i class="fa fa-arrow-left"></i> Back to {{{$candidate->name}}}</a>
    <a class="btn btn-default btn-lg btn-full"  href="{{{$contest->canonical_url}}}"><i class="fa fa-arrow-left"></i> Back to Pick</a>
  </div>
  <script>
    function share(response)
    {
      FB.ui({
        method: 'share',
        href: {{json_encode($candidate->canonical_url($contest))}},
      });
    }
  </script>

@stop
