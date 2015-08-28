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
@elseif($state == 'home')
  <?php $u = Auth::user();?>
  @if(!$u)
  <h1>Welcome to Pick.Cool</h1>
  <hr />
  @endif

<div class="leaderboard">
  
  @if($u && $u->is_visible)
    <h2>Game Time!</h2>
    <p>Prove you are the arbiter of cool by voting on Pick.Cool.</p>
  @elseif(!$u)
    <h2>Join the Game!</h2>
    <p>To climb the Pick.Cool ranks or enter a pick you must first <a href="/login?success=/">Log In</a>.</p>
  @else
    <h2>Join the Game!</h2>
    <p>See your Pick.Cool rank by <a href="/my/set_visible?r=/">making your profile visible.</a></p>
  @endif
  <h2>How to Play</h2>
  <p><b class="text-success">Earn points by picking winners early.</b></p>
  <p>You earn points by voting. When someone votes for your pick after you did, you get a point. Earn maximum points by picking winners early.</p>
  <p>The <b class="text-success">Earned</b> score is from closed picks. The <b class="text-muted">Pending</b> score is from open picks and may still change.</p>
  <p><strong class="text-danger">Changing your vote will reset your Pending score.</strong></p>
  <hr/>
  <h2>Pick.Cool Leaderboard</h2>
  <div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th class="col-sm-1">Rank</th>
        <th class="col-sm-1 profile-image-label"></th>
        <th class="col-sm-2">Name</th>
        <th class="col-sm-1">Earned</th>
        <th class="col-sm-1">Pending</th>
        <th class="hidden-xs col-sm-6"></th>
      </tr>
    </thead>
    @foreach($users as $u)
      <tr>
        <td>
          <span class="badge">{{nth($u->rank)}}</span>
        </td>
        <td class="td-no-padding">
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
        <td class="hidden-xs"></td>
      </tr>
    @endforeach
  </table>
  </div>
  {{$users->links()}}
</div>
@elseif($state == 'live')
  <div class="row">
    <div style="margin-bottom:20px;" class="col-sm-12">
      <h2>Bummer. We don't have any live picks. Check back with us soon! You can explore the <a href="/archived">archive</a> while you wait.</h2>
    </div>
  </div>
@endif
@stop