@extends('contests.candidates.layout')



@section('contests.candidates.content')
  <div class="row">
    <div class="col-xs-4">
      <div class="candidate-small">
        <img src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
      </div>
    </div>
    <div class="col-xs-8 text-success" style="font-size: 19px;">
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
    @if($contest->ticket_url)
      <hr />
      <div style="text-align:center;">
        <a style="color:black;" href="http://www.janugget.com/entertainment/celebrity-showroom.html">Music, Models, and Ink Awards Ceremony. February 19.</a>
        <a href="http://www.janugget.com/entertainment/celebrity-showroom.html"><img style="max-width:150px;margin:0 auto;" alt="John Ascuaga's Nugget" class="img-responsive" src="/assets/img/nugget-color-logo.jpg" /></a>
      </div>
      <hr />     
    @endif
  <h1>To vote is awesome, to share is divine. Help your pick win by sharing with friends.</h1>
  <button class="btn btn-primary btn-lg btn-full" onclick="share()"><i class="fa fa-facebook"></i> Share Now</button>

  <div style="color: gray; font-size: 14px">
    <p>Awesome sauce, you voted for {{{$candidate->name}}}.</p>
    <p>You can only vote for ONE person, but you can change your vote any time.</p>
    <p>To help even more, share this page with your friends.</p>
    @if (isset($nextContest) && $nextContest != null)
    <p>Now it is time to vote for {{ $nextContest->title }}</p>
    @endif
  </div>
  <div style="margin-top: 20px">
  </div>
  @if (isset($nextContest) && $nextContest != null)
  <div class="contest">
	<h2 class="title-header">
	<a class="title" href="{{{$nextContest->canonical_url}}}">{{{$nextContest->title}}}</a>
	</h2>
	<div class="votes-total">
	  <i class="fa fa-check-square"></i> 
		{{{$nextContest->vote_count_0}}} Votes
	  @if($nextContest->writein_enabled && !$nextContest->is_ended)| 
	  	<span class="text-success">OPEN pick - Join Now</span>
	  @endif      
	  @if($nextContest->is_ended)| 
	  	<span class="text-danger" ng-if="$contest->is_ended">Voting has ended.</span>
	  @endif
	</div>
	@if($nextContest->is_editable)
	  <a class="btn btn-xs btn-success" href="r('contest.edit', [$nextContest->id])">Edit</a>
	@endif
	<div class="clearfix"></div>
	<ul class="list-inline" style="margin-left: 0px; margin-bottom: 15px">
	  @foreach($nextContest->candidates->take(5) as $tmpCandidate)
		<li>
		  <a class="candidate-small" 
		  	href="{{{$nextContest->canonical_url}}}"  
		  	class="{{{$nextContest->current_user_candidate_id == $tmpCandidate->id ? 'selected' : '' }}}">
			<img src="/loading.gif" data-echo="{{{$tmpCandidate->image_url('thumb')}}}" 
				alt="{{{$tmpCandidate->name}}}" title="Vote for {{{$tmpCandidate->name}}}">
			<div class="clearfix">
			  <div class="badges pull-right">
				@if($tmpCandidate->is_on_fire)
				  <span class="badge badge-fire" 
				  	title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours.">
				  <i class="fa fa-fire"></i></span>
				@endif
				@foreach($tmpCandidate->badges as $badge)
				  <span class="badge badge-giver" 
				  title="Pledges 25% or more of cash winnings to {{{$badge->pivot->charity_name}}}."><i class="fa fa-heart"></i></span>
				@endforeach
				<span class='badge badge-vote-count' title="{{{$tmpCandidate->vote_count_0}}} votes">
					{{{$tmpCandidate->vote_count_0}}}
				</span>
			  </div>
			</div>
		  </a>
		</li>
	  @endforeach
	  @if($nextContest->candidates->count()>5)
		<li ><a href="{{{$nextContest->canonical_url}}}">...{{{$nextContest->candidates->count()-5}}} more</a></li>
	  @endif
	</ul>
	</div>
    @endif
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
