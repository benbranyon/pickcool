@extends('app')

@section('content')
  <h2>How to Play</h2>
  <ul>
  <li><b class="text-success">Earn points by picking winners early.</b></li>
  <li>You earn points by voting. When someone votes for your pick after you did, you get a point. Earn maximum points by picking winners early.</li>
  <li>The <b class="text-success">Points</b> are final when the pick closes. If you have points in open picks, they still count but are shown <b class="text-muted">(<i class="fa fa-arrow-up"></i>like this)</b> next to your score since they may change.</li>
  <li><strong class="text-danger">If you change your vote in an open pick, you restart the points you've earned for that pick.</strong></li>
  </ul>
  <hr />
  <h1>Pick.Cool Leaderboard</h1>
  <hr/>
<div class="leaderboard">
  <div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th class="col-sm-1">Rank</th>
        <th class="col-sm-1 profile-image-label"></th>
        <th class="col-sm-2">Name</th>
        <th class="col-sm-1">Points</th>
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
        <td>
          <strong><span class="text-success">{{$u->earned_points+$u->pending_points}}</span></strong>
          @if($u->pending_points)
            <span class="text-muted">(<i class="fa fa-arrow-up"></i>{{$u->pending_points}})</span>
          @endif
        </td>
        <td class="hidden-xs"></td>
      </tr>
    @endforeach
  </table>
  </div>
  {{$users->links()}}
</div>
@stop