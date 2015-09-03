@extends('contests.candidates.layout')

@section('contests.candidates.content')
  <div class="candidate-single">
    <div class='row'>
      <div class="col-xs-12">
        <h1>Public Voters</h1>
        <div class="leaderboard">
          <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th class="col-sm-4" style="text-align:center;">Name</th>
                <th class="col-sm-4">Vote Time</th>
                <th class="col-sm-4">Points This Pick</th>
              </tr>
            </thead>
            @foreach($votes as $v)
              <?php
              $u = $v->user;
              ?>
              @if(isset($u))
              <tr>
                <td style="text-align:center;" class="td-no-padding">
                  <a href="{{$u->profile_url}}">
                    <img class="profile-img" src="{{$u->profile_image_url}}"/>
                  </a>
                  <br/>
                  <a href="{{$u->profile_url}}">
                    {{$u->full_name}}
                    <div>
                      <span class="text-success">{{$u->earned_points+$u->pending_points}}</span>
                      points
                    </div>
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
                      <span class="text-success">{{$v->votes_ahead}}</span>
                    @else
                      <span class="text-muted"><i class="fa fa-arrow-up"></i> {{$v->votes_ahead}}</span>
                    @endif
                    points
                  @endif
                  
                </td>
              </tr>
            @endif
            @endforeach
          </table>
          </div>
          {{$votes->links()}}
        </div>
      </div>
    </div>
  </div>
@stop
