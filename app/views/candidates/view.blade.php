@extends('app')

@section('head')
<title>{{{$candidate->name}}} in {{{$contest->title}}} | pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote for {{{$candidate->name}}} in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$candidate->canonical_url($contest)}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{$candidate->image_url('facebook')}}}?_c={{microtime(true)}}"/>
<meta property="fb:admins" content="{{{$candidate->user->fb_id}}}"/>
@stop

@section('content')
  <div class="view" style="text-align: center; max-width: 320px; width:100%; margin-left: auto; margin-right: auto;">
    <h1>
      {{{$candidate->name}}}
    </h1>
    <h2><a href="{{{$contest->canonical_url}}}">{{$contest->title}}</a></h2>
    <h3>{{$candidate->vote_count_0}} votes | <a href="#comments"><fb:comments-count href="{{{$candidate->canonical_url}}}"></fb:comments-count> friendly comments</a></h3>
    <div id="candidate" class="candidate-large {{{$candidate->is_user_vote ? 'selected' : ''}}}">
      <img src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
    </div>
    <table class="table badges">
      @if($candidate->is_on_fire)
        <tr>
          <td>
            <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
          </td>
          <td align=left>
            {{{$candidate->name}}} is on fire because the vote count has increased by {{{Candidate::$on_fire_threshold*100}}}% or more in the last 24 hours. Congratulations!
          </td>
        </tr>
      @endif
      @if($candidate->is_giver)
        <tr>
          <td>
            <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span>
          </td>
          <td align=left>
            {{{$candidate->name}}} is a Charitable Giver and has pledged 25% or more of cash winnings to <a href="{{{$candidate->charity_url}}}">{{{$candidate->charity_name}}}</a>.
          </td>
        </tr>
      @endif

    </table>
    
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

    <div id="comments" class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Friendly Comments</h3>
      </div>
      <div class="panel-body" style="padding-left: 0px; padding-right: 0px;padding-top:0px">
        <div class="fb-comments" data-href="{{{$candidate->canonical_url}}}" data-numposts="5" data-colorscheme="light" data-width="100%"></div>
      </div>
    </div>          
    

  </div>
  <script>
    function share()
    {
      FB.ui({
        method: 'share',
        href: {{json_encode($candidate->canonical_url($contest))}},
      });
    }
  </script>

@stop
