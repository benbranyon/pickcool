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
  <div class="list">
    @if($state == 'hot')
      <h1>Hot Picks</h1>
      <hr />
    @elseif($state == 'new')
      <h1>New Picks</h1>
      <hr />
    @elseif($state == 'top')
      <h1>Top Picks</h1>
      <hr />
    @endif
    @foreach($contests as $contest)
      <div class="contest">
        <h2 class="title-header"><a class="title" href="{{{$contest->canonical_url}}}">{{{$contest->title}}}</a></h2>
        <div class="votes-total">
          <i class="fa fa-check-square"></i> 
            {{{$contest->vote_count_0}}} Votes
          @if($contest->writein_enabled && !$contest->is_ended)| <span class="text-success">OPEN pick - Join Now</span>@endif      
          @if($contest->is_ended)| <span class="text-danger" ng-if="$contest->is_ended">Voting has ended.</span>@endif
        </div>
        @if($contest->is_editable)
          <a class="btn btn-xs btn-success" href="{{r('admin.contests.edit', [$contest->id])}}">Edit</a>
        @endif
        <div class="clearfix"></div>
        <ul class="list-inline" style="margin-left: 0px; margin-bottom: 15px">
          @foreach($contest->candidates->take(5) as $candidate)
            <li>
              <a class="candidate-small" href="{{{$contest->canonical_url}}}"  class="{{{$contest->current_user_candidate_id == $candidate->id ? 'selected' : '' }}}">
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
@stop