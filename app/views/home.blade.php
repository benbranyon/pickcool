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
@if(!$contests->isEmpty())
  <div class="list">
    @if($state == 'live')
      <h1>Live Picks</h1>
      <hr />
    @elseif($state == 'archived')
      <h1>Archived Picks</h1>
      <hr />
    @elseif($state == 'home')
      <h1>Live Picks</h1>
      <hr />
    @endif
    @foreach($contests as $contest)
      <div class="contest contest-{{$contest->id}}">
        <h2 class="title-header"><a class="title" href="{{{$contest->canonical_url}}}">{{{$contest->title}}}</a></h2>
        <div class="votes-total">
          <i class="fa fa-check-square"></i> 
          {{{$contest->vote_count_0}}} votes
          @if($contest->writein_enabled && !$contest->is_ended)
            | <span class="text-success">OPEN pick - Join Now</span>
          @endif      
          @if($contest->is_ended)
            | <span class="text-danger" ng-if="$contest->is_ended">Voting has ended.</span>
          @endif
        </div>
        <a class="is-editable hidden btn btn-xs btn-success" href="{{r('admin.contests.edit', [$contest->id])}}">Edit</a>
        <div class="clearfix"></div>
        <ul class="list-inline" style="margin-left: 0px; margin-bottom: 15px">
          @foreach($contest->candidates->take(5) as $candidate)
            <li>
              <a class="candidate-small candidate-{{$candidate->id}}" href="{{{$contest->canonical_url}}}" >
                <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                <div class="clearfix">
                  <div class="badges pull-right">
                    @if($candidate->is_on_fire)
                      <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
                    @endif
                    @foreach($candidate->badges as $badge)
                      <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$badge->pivot->charity_name}}}."><i class="fa fa-heart"></i></span>
                    @endforeach
                    <span class='badge badge-vote-count' title="{{{$candidate->vote_count_0}}} votes">
                        {{{$candidate->vote_count_0}}}
                    </span>
                  </div>
                </div>
              </a>
            </li>
          @endforeach
          @if($contest->candidates->count()>5)
            <li ><a href="{{{$contest->canonical_url}}}">...{{{$contest->candidates->count()-5}}} more</a></li>
          @endif
        </ul>
      </div>
    @endforeach
  </div>
@else
  <h1>Welcome to Pick.Cool</h1>
  <hr />

<div class="leaderboard">
  <h2>Join the Game!</h2>
  <h3>How to Play</h3>
  <p><b class="text-success">Earn points by picking winners early.</b></p>
  <p>Prove you are the arbiter of cool by voting on Pick.Cool.
  <p>You earn points by voting. When someone votes for your pick after you did, you get a point. Earn maximum points by picking winners early.
  <p>The <b class="text-success">Earned</b> score is from closed picks. The <b class="text-muted">Pending</b> score is from open picks and may still change.
  <hr/>
  <h2>Leaderboard</h2>
  <table>
    <thead>
      <tr>
        <th>Rank</th>
        <th></th>
        <th>Name</th>
        <th>Earned</th>
        <th>Pending</th>
      </tr>
    </thead>
    @foreach($users as $u)
      <tr>
        <td>
          <span class="badge">{{nth($u->rank)}}</span>
        </td>
        <td>
          <a href="{{$u->profile_url}}">
            <img class="profile-img" src="{{$u->profile_image_url}}"/>
          </a>
        </td>
        <td>
          <a href="{{$u->profile_url}}">
            {{$u->full_name}}
          </a>
        </td>
        <td class="text-success">
          <span class="glyphicon glyphicon-star" aria-hidden="true"></span>{{$u->earned_points}}
        </td>
        <td class="text-muted">
          <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>{{$u->pending_points}}
        </td>
      </tr>
    @endforeach
  </table>
  {{$users->links()}}
</div>
@endif
@stop