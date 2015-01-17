@extends('contests.layout')

@section('contests.content')
  @if($contest->can_vote)
    <p>
      <b><i>Choose your favorite pick below and cast your vote.</i></b>
    </p>
  @endif

  <ul class="list-inline clearfix" style="text-align: center">
    @if($contest->can_join && $contest->password)
      <li>
        @if(Session::get('contest_view_mode','s')=='s')
          <a class="candidate-small" href="{{{$contest->join_url}}}">
            <img src="/add-user.png" alt="You" title="Join the Pick" />
            <div class="overlay">
              <div class="btn btn-xs btn-success">Join Now!</div>
            </div>
          </a>
        @else
          <div >
            <a class="candidate-large" href="{{{$contest->join_url}}}">
              <img src="/add-user.png" alt="You" title="Join the Pick" />
              <div class="overlay">
                <div class="btn btn-lg btn-success">Join Now!</div>
              </div>
            </a>
          </div>
        @endif
      </li>
    @endif
  
    @foreach($contest->candidates as $candidate)
      <?php if(Input::get('f','')=='g' && !$candidate->charity_name) continue; ?>
      <li >
        @if(Session::get('contest_view_mode','s')=='s')
          <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
            <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
            <div class="clearfix">
              <div class="badges pull-right">
                @if($candidate->is_on_fire)
                  <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
                @endif
                @if($candidate->is_giver)
                  <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span>
                @endif
                <span class='badge badge-vote-count' title="{{{$candidate->vote_count_0}}} votes">{{{$candidate->vote_count_0}}}</span>
              </div>
            </div>
          </a>
        @else
          <div >
            <h1 class="big">
              {{{$candidate->name}}}
            </h1>
            <h2>
              {{{$candidate->vote_count_0}}} votes
            </h2>
            <a class="candidate-large {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
              <img src="/loading.gif" data-echo="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
              <div class="clearfix">
                <div class="badges pull-right">
                  @if($candidate->is_on_fire)
                    <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
                  @endif
                  @if($candidate->is_giver)
                    <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span>
                  @endif
                </div>
              </div>
            </a>
          
            <a class="btn btn-md btn-primary btn-half" href="{{{$candidate->canonical_url($contest)}}}"><i class="fa fa-camera"></i> More</a>
            @if($candidate->is_user_vote)
              <a class="btn btn-md btn-warning btn-half" href="{{{$candidate->unvote_url}}}"><i class="fa fa-close"></i> Unvote</a>
            @else
              <a class="btn btn-md btn-primary btn-half" href="{{{$candidate->vote_url}}}"><i class="fa fa-check"></i> Vote</a>
            @endif
            <button class="btn btn-md btn-primary btn-half" onclick="share({{{json_encode($candidate->canonical_url)}}})"><i class="fa fa-facebook"></i> Share</button>
            <hr/>
          </div>
        @endif
      </li>
    @endforeach
  
    @if($contest->can_join)
      <li>
        @if(Session::get('contest_view_mode','s')=='s')
          <a class="candidate-small" href="{{{$contest->join_url}}}">
            <img src="/add-user.png" alt="You" title="Join the Pick" />
            <div class="overlay">
              <div class="btn btn-xs btn-success">Join Now!</div>
            </div>
          </a>
        @else
          <div >
            <h1 class="big">You</h1>
            <a class="candidate-large" href="{{{$contest->join_url}}}">
              <img src="/add-user.png" alt="You" title="Join the Pick" />
              <div class="overlay">
                <div class="btn btn-lg btn-success">Join Now!</div>
              </div>
            </a>
            <button disabled=true class="btn btn-md btn-primary btn-half"><i class="fa fa-camera"></i> More</button>
            <button disabled=true class="btn btn-md btn-primary btn-half" ><i class="fa fa-check"></i> Vote</button>
            <button disabled=true class="btn btn-md btn-primary btn-half"><i class="fa fa-facebook"></i> Share</button>
          </div>
        @endif
      </li>
    @endif
  </ul>

<script>
function share(url)
{
  FB.ui({
    method: 'share',
    href: url,
  });
}
</script>

@stop
