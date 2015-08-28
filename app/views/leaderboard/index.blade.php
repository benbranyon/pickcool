@extends('app')

@section('content')
  <h1>Pick.Cool Leaderboard</h1>
  <hr/>
<div class="leaderboard">
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
  <hr />

  <h2>How to Play</h2>
  <p><b class="text-success">Earn points by picking winners early.</b></p>
  <p>Prove you are the arbiter of cool by voting on Pick.Cool.
  <p>You earn points by voting. When someone votes for your pick after you did, you get a point. Earn maximum points by picking winners early.
  <p>The <b class="text-success">Earned</b> score is from closed picks. The <b class="text-muted">Pending</b> score is from open picks and may still change.
  <p><strong class="text-danger">Changing your vote will reset your Pending score.</strong></p>

  {{$users->links()}}
</div>
@stop