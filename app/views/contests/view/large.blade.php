@extends('contests.layout')

@section('contests.content')
  <ul class="list-inline clearfix" style="text-align: center">
    @if($contest->can_join && $contest->password)
      <li>
        <div >
          <a class="candidate-large" href="{{{$contest->join_url}}}">
            <img src="/add-user.png" alt="You" title="Join the Pick" />
            <div class="overlay">
              <div class="btn btn-lg btn-success">Join Now!</div>
            </div>
          </a>
        </div>
      </li>
    @endif
  
    @foreach($contest->candidates as $candidate)
      <?php if(Input::get('f','')=='g' && !$candidate->charity_name) continue; ?>
      <li >
        <div >
          <h1 class="big">
            {{{$candidate->name}}}
          </h1>
          <h2>{{{$candidate->vote_count_0}}} votes</h2>
          <a class="candidate-large {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
            <img src="/loading.gif" data-echo="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
            <div class="clearfix">
              <div class="badges pull-right">
                @if($candidate->is_on_fire)
                  <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
                @endif
                @foreach($candidate->badges as $badge)
                  <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$badge->pivot->charity_name}}}."><i class="fa fa-heart"></i></span>
                @endforeach
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
      </li>
    @endforeach
  
    @if($contest->can_join)
      <li>
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
