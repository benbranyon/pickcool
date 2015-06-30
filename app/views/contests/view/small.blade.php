@extends('contests.layout')

@section('contests.content')
  <ul class="list-inline clearfix" style="text-align: center">
    @if($contest->can_join && $contest->password)
      <li>
        <a class="candidate-small" href="{{{$contest->join_url}}}">
          <img src="/add-user.png" alt="You" title="Join the Pick" />
          <div class="overlay">
            <div class="btn btn-xs btn-success">Join Now!</div>
          </div>
        </a>
      </li>
    @endif
  
    @foreach($contest->candidates as $candidate)
      <?php $candidate->contest = $contest; ?>
      <?php if(Input::get('f','')=='g' && !$candidate->charity_name) continue; ?>
      <li >
        <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
          <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
          <div class="clearfix">
            <div class="badges pull-right">
              @if($candidate->is_on_fire)
                <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
              @endif
              @foreach($candidate->badges as $badge)
                <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$badge->pivot->charity_name}}}."><i class="fa fa-heart"></i></span>
              @endforeach
              <span class='badge badge-vote-count' title="{{{$candidate->vote_count_0}}} votes">{{{$candidate->vote_count_0}}}</span>
            </div>
          </div>
        </a>
      </li>
    @endforeach
  
    @if($contest->can_join)
      <li>
        <a class="candidate-small" href="{{{$contest->join_url}}}">
          <img src="/add-user.png" alt="You" title="Join the Pick" />
          <div class="overlay">
            <div class="btn btn-xs btn-success">Join Now!</div>
          </div>
        </a>
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
