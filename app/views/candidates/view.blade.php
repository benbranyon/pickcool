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
  <div class="view" style="text-align: center; max-width: 320px; width:100%; margin-left: auto; margin-right: auto;">
    <h1>
      {{{$candidate->name}}}
    </h1>
    <h2><a href="{{{$contest->canonical_url}}}">{{$contest->title}}</a></h2>
    @if(!$candidate->image_id)
      <div class="alert alert-warning">
        Please note - the image for this voting profile is under review. No voting can take place until the image has been approved.
      </div>
    @else
      @if(Auth::user() && $candidate->user_id == Auth::user()->id && $candidate->has_pending_images)
        <div class="alert alert-warning">
          You recently submitted one or more images for review. We will notify you when our review is complete. Please allow 24-48 hours.
        </div>
      @endif
      @if($contest->sponsors->count()>0)
        <?php $sponsor = $contest->random_sponsor; ?>
        <h2>Sponsored by: <a href="{{{$sponsor->url}}}" target="_self">{{{$sponsor->name}}}</a></h2>
      @endif
      <h3>{{$candidate->vote_count_0}} votes</h3>
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
              {{{$candidate->name}}} is a Charitable Giver and has pledged either 25% of cash winnings or 4 hours of service, or more, to <a href="{{{$candidate->charity_url}}}">{{{$candidate->charity_name}}}</a>.
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
    @endif
    @if($candidate->is_writein)
      <a class="btn btn-lg btn-warning btn-full" href="{{{$candidate->refresh_url}}}"><i class="fa fa-facebook"></i> Refresh Your Picture</a>
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
  </script>

@stop
