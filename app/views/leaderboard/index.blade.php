@extends('app')

@section('content')
<div class="leaderboard">
  <h1>Leaderboard</h1>
  <p>You earn points for playing on Pick.Cool. The more earn, the more powerful of an influencer you become. 
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
          #{{$u->rank}}
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
        <td>
          {{$u->total_points}}
        </td>
        <td>
          {{$u->pending_points}}
        </td>
      </tr>
    @endforeach
  </table>
  {{$users->links()}}
</div>
@stop