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
          <h2>Total Earned Points: {{$user->earned_points}}</h2>
          <h2>Total Pending Points: {{$user->pending_points}}</h2>
          <h2><a href="https://www.facebook.com/app_scoped_user_id/{{$user->fb_id}}"><i class='fa fa-large fa-facebook-square'></i></a>
        </div>
      </div>
      <hr/>
      <div class="row">
        <div class="col-xs-12">
          <h1>How to Play</h1>
          <p><b class="text-success">Earn points by picking winners early.</b></p>
          <p>Prove you are the arbiter of cool by voting on Pick.Cool.
          <p>You earn points by voting. When someone votes for your pick after you did, you get a point. Earn maximum points by picking winners early.
          <p>The <b class="text-success"><span class="glyphicon glyphicon-star" aria-hidden="true">Earned</b> score is from closed picks. The <b class="text-muted"><span class="glyphicon glyphicon-plus" aria-hidden="true">Pending</b> score is from open picks and may still change.
        </div>
      </div>
      <hr/>
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