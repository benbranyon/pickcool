@extends('contests.candidates.layout')

@section('contests.candidates.content')
  <div class="candidate-single">
    <div class='row'>
      <div class="col-xs-12">
        <h1>Public Voters</h1>
        <div class="leaderboard">
          <table>
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Vote Time</th>
                <th>Points</th>
              </tr>
            </thead>
            @foreach($votes as $v)
              <?php
              $u = $v->user;
              ?>
              @if(isset($u))
              <tr>
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
                  {{$v->voted_at->diffForHumans()}}
                </td>
                <td>
                  @if($u->is_in_contest($contest))
                    <span class="text-warning"><i>in pick</i></span>
                  @else
                    @if($contest->is_ended)
                      <span class="text-success"><span class="glyphicon glyphicon-star" aria-hidden="true"></span>{{$v->votes_ahead}} earned</span>
                    @else
                      <span class="text-muted"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>{{$v->votes_ahead}} pending</span>
                    @endif
                  @endif
                  
                </td>
              </tr>
            @endif
            @endforeach
          </table>
          {{$votes->links()}}
        </div>
      </div>
    </div>
  </div>
@stop
