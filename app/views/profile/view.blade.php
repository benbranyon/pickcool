@extends('app')

@section('head')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.min.css">
<script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
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
        <div class="col-xs-5">
          <img class="profile-img" src="{{$user->profile_image_url}}"/>
        </div>
        <div class="col-xs-7">
          <h1>{{$user->full_name}}</h1>
          <h2>Overall Standing: #{{$user->rank}}</h2>
          <h2>Total Earned Points: {{$user->total_points}}</h2>
          <h2>Total Pending Points: {{$user->pending_points}}</h2>
          <h2><a href="https://www.facebook.com/app_scoped_user_id/{{$user->fb_id}}"><i class='fa fa-large fa-facebook-square'></i></a>
        </div>
      </div>
      <hr/>
      <h1>Track Record</h1>
      @foreach(Contest::whereHas('votes', function($q) use ($user) {
          $q->where('user_id', '=', $user->id);
      })->whereRaw('ends_at >= utc_timestamp()')->orderBy('ends_at')->get() as $contest)
        {{View::make('profile.row', ['user'=>$user, 'contest'=>$contest, 'candidate'=>$user->current_vote_for($contest)->candidate, 'style'=>'muted', 'status'=>'pending'])}}
      @endforeach
      @foreach(Contest::whereHas('votes', function($q) use ($user) {
          $q->where('user_id', '=', $user->id);
      })->whereRaw('ends_at < utc_timestamp()')->orderBy('ends_at')->get() as $contest)
        {{View::make('profile.row', ['user'=>$user, 'contest'=>$contest, 'candidate'=>$user->current_vote_for($contest)->candidate, 'style'=>'success', 'status'=>'earned'])}}
      @endforeach
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