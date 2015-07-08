<?php
$u = Auth::user();
$the_url = route(Input::get('r'), Input::get('p'));
?>
<div class="clearfix">
  <ul class="subnav list-inline pull-right">
    @if($u)
      <li>Welcome, {{{$u->first_name}}}.</li>
      <li><a href="{{$u->profile_url}}">Profile</a></li>
    @endif
    <li><a href="/faq"><i class="fa fa-question-circle"></i> F.A.Q.</a>
    @if($u)
      <li><a href="{{{r('inbox')}}}" class="{{{$u->has_unread_messages ? 'unread' : ''}}}"><i class="fa fa-envelope"></i></a></li>
      <li><a href="{{{r('logout', ['success'=>r('home')])}}}">Logout</a></li>
    @else
      <li><a href="{{{
        r(
          'login', 
          Input::get('r')==route('login') ? [] : [
            'success'=>$the_url,
            'cancel'=>$the_url
          ]
        )
      }}}">Log in</a></li>
    @endif
  </ul>
  <div class="clearfix"></div>
  <ul class="subnav list-inline pull-right">
      @if($u && $u->is_visible)
        <li><a href="{{{route('leaderboard')}}}">You are in {{nth($u->rank)}} place with {{$u->total_points}} points. What's this?</a>
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