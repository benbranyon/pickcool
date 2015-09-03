<?php
$u = Auth::user();
$the_url = route(Input::get('r'), Input::get('p'));
$login_url =         r(
          'login', 
          Input::get('r')==route('login') ? [] : [
            'success'=>$the_url,
            'cancel'=>$the_url
          ]
        );
?>
<div class="clearfix">
  <ul class="subnav list-inline pull-right">
    @if($u)
      <li><a href="{{$u->profile_url}}">Welcome, {{{$u->first_name}}}</a>.</li>
      <li>
        @if($u && $u->is_visible)
          <span class="badge">{{nth($u->rank)}}</span> | 
        @endif
        <strong><span class="text-success">{{$u->earned_points+$u->pending_points}}</span></strong>
        @if($u->pending_points)
          <span class="text-muted">(<i class="fa fa-arrow-up"></i>{{$u->pending_points}})</span>
        @endif
        points
      </li>
    @endif
    @if($u)
      <li><a href="{{{r('inbox')}}}" class="{{{$u->has_unread_messages ? 'unread' : ''}}}"><i class="fa fa-envelope"></i></a></li>
      <li><a href="{{{r('logout', ['success'=>r('home')])}}}"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span></a></li>
    @endif
  </ul>
  <div class="clearfix"></div>
  <ul class="subnav list-inline pull-right">
      @if($u && $u->is_visible)
        <li>
          You are in 
          <span class="badge">{{nth($u->rank)}}</span> 
          place with 
          <strong><span class="text-success">{{number_format($u->earned_points+$u->pending_points)}}</span></strong> 
          @if($u->pending_points)
            <span class="text-muted">(<i class="fa fa-arrow-up"></i>{{$u->pending_points}})</span>
          @endif
          points.
          <a href="{{{route('leaderboard')}}}">What's this?</a>
      @elseif(!$u)
        <div class="alert alert-success">
          {{number_format(User::count())}} users are earning points on Pick.Cool by voting for their favorites. Check out the 
          <a href="{{{route('leaderboard')}}}">leaderboard</a> and <a href="{{{$login_url}}}">log in</a> to play.
        </div> 
      @endif
  </ul>
</div>
@if($u && $u->has_messages && $u->has_unread_messages && Route::currentRouteName()!='inbox')
  <div class="alert alert-warning">
    You have important unread messages. <a href="{{{r('inbox')}}}">Check your inbox now.</a>
  </div>
@endif
@foreach(['success', 'warning', 'danger'] as $kind)
  @if(Session::get($kind))
    <div class="alert alert-{{{$kind}}}">
      {{Session::get($kind)}}
    </div>
  @endif
@endforeach
@if($u && !$u->is_visible)
  <div class="alert alert-success">
    <b>Get in the game!</b> <a href="{{route('my.set_visible', ['r'=>$the_url])}}">Make your voting profile visible.</a>
  </div>
@endif