@extends('app')

@section('content')
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
  <hr />

  <h2>How to Play</h2>
  <ul>
  <li><b class="text-success">Earn points by picking winners early.</b></li>
  <li>You earn points by voting. When someone votes for your pick after you did, you get a point. Earn maximum points by picking winners early.</li>
  <li>The <b class="text-success">Earned</b> score is from closed picks. The <b class="text-muted">Pending</b> score is from open picks and may still change.</li>
  <li><strong class="text-danger">Changing your vote will reset your Pending score.</strong></li>
  </ul>
  {{$users->links()}}
</div>
@stop