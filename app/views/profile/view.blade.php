@extends('app')

@section('head')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.min.css">
    <script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
    <title>{{{$user->full_name}}} | pick.cool</title>
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{{$user->full_name}}}"/>
    <meta property="og:site_name" content="pick.cool"/>
    <meta property="og:url" content="{{{$user->profile_url}}}"/>
    <meta property="og:description" content="{{{$user->full_name}}} Pick.Cool #{{{$user->rank}}} Picker | Total Earned Points: {{{$user->earned_points}}}"/>
    <meta property="og:image" content="https:{{{$user->profile_image_url}}}"/>
@stop


@section('content')
  @if($is_self && !$user->is_visible)
    <div class="alert alert-danger">
      Your profile is hidden.
    </div>
  @endif
  <div class="profile">
    @if($user->is_visible)
      <div class="row">
        <div class="col-xs-5 col-sm-3">
          <img class="profile-img" src="{{$user->profile_image_url}}"/>
        </div>
        <div class="col-xs-7">
          <h1>{{$user->full_name}}</h1>
          <h2>Overall Standing: #{{$user->rank}}</h2>
          <h2>Total Points: 
            <strong><span class="text-success">{{$user->earned_points+$user->pending_points}}</span></strong>
            @if($user->pending_points)
              <span class="text-muted">(<i class="fa fa-arrow-up"></i>{{$user->pending_points}})</span>
            @endif
          </h2>
          <p><a href="https://www.facebook.com/app_scoped_user_id/{{$user->fb_id}}"><i class='fa fa-large fa-facebook-square'></i></a></p>
        </div>
      </div>
      <hr/>
      @if(isset($current_contests) && !empty($current_contests))
        <h1>Active Picks</h1>
        <ul>
        @foreach($current_contests as $contest)
          <?php $candidate = Candidate::find($contest->id);?>
          <?php $contest_data = Contest::find($contest->contest_id);?>
          <li><a href="{{{$candidate->canonical_url($contest_data)}}}"  >{{$contest->title}}</a></li>
        @endforeach
        </ul>
        <hr/>
      @endif
      
      @if(isset($past_contests) && !empty($past_contests))
        <h1>Past Picks</h1>
        <ul>
        @foreach($past_contests as $past_contest)
          <?php $candidate = Candidate::find($past_contest->id);?>
          <?php $contest_data = Contest::find($past_contest->contest_id);?>
          <li><a href="{{{$candidate->canonical_url($contest_data)}}}"  >{{$past_contest->title}}</a></li>
        @endforeach
        </ul>
        <hr/>
      @endif
      <h1>Track Record</h1>
      <div class="track-record">
        @foreach($open_candidates as $candidate)
          {{View::make('profile.row', ['user'=>$user, 'contest'=>$candidate->contest, 'candidate'=>$candidate, 'style'=>'muted', 'status'=>'pending', 'icon'=>'plus'])}}
        @endforeach
        @foreach($closed_candidates as $candidate)
          {{View::make('profile.row', ['user'=>$user, 'contest'=>$candidate->contest, 'candidate'=>$candidate, 'style'=>'success', 'status'=>'earned', 'icon'=>'star',])}}
        @endforeach
      </div>
    @endif
        
    @if($is_self)
      <hr/>
      <h1>Preferences</h1>
      <p>Allow others to see your profile and voting activity.
      <p><input type="checkbox" id='play-game' data-on-text="Show" data-off-text="Hide" {{Auth::user()->is_visible ? 'checked' : ''}}/>
      <script>
        (function($) {
          $(function() {
            $("#play-game")
            .bootstrapSwitch()
            .on('switchChange.bootstrapSwitch', function(event, state) {
              $.get('{{route('api.profile.settings')}}', {is_visible: state ? 1 : 0}, function() {
                window.location = document.location;
              });
            })
            ;
          });
        })(jQuery);
      </script>
    @endif
  </div>
@stop



@section('foot')
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/js/bootstrap-switch.min.js"></script>
@stop