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
      <li>Welcome, {{{$u->first_name}}}.</li>
      @if($u->is_visible)
        <li><a href="{{$u->profile_url}}"><span class="badge">{{nth($u->rank)}}</span>|<span class="text-success"><span class="glyphicon glyphicon-star" aria-hidden="true"></span>{{$u->earned_points}}</span>|<span class="text-muted"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>{{$u->pending_points}}</span></a></li>
      @endif
      <li><a href="{{{r('inbox')}}}" class="{{{$u->has_unread_messages ? 'unread' : ''}}}"><i class="fa fa-envelope"></i></a></li>
      <li><a href="{{{r('logout', ['success'=>r('home')])}}}"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span></a></li>
    @endif
  </ul>
  <div class="clearfix"></div>
  <ul class="subnav list-inline pull-right">
    <li class="alert alert-success">
      {{number_format(User::count())}} users are earning points on Pick.Cool by voting for their favorites.
      <p>  
      <a class="btn btn-primary btn-xs" href="{{{route('leaderboard')}}}">Go to the Leaderboard</a>
      @if(!$u)
        <a class="btn btn-success btn-xs" href="{{{$login_url}}}">Log In</a>
      @endif
    </li>
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