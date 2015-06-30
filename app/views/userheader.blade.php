<div class="clearfix">
  <ul class="subnav list-inline pull-right">
    @if(Auth::user())
      <?php $candidate = Candidate::whereUserId(Auth::user()->id)->orderBy('created_at', 'desc')->first(); ?>
      @if($candidate)
        <li><a href="{{{$candidate->canonical_url}}}">My Pick</a>
      @else
        <li>Welcome, {{{Auth::user()->first_name}}}.</li>
      @endif
    @endif
    <li><a href="/faq"><i class="fa fa-question-circle"></i> F.A.Q.</a>
    @if(Auth::user())
      <li><a href="{{{r('inbox')}}}" class="{{{Auth::user()->has_unread_messages ? 'unread' : ''}}}"><i class="fa fa-envelope"></i></a></li>
      <li><a href="{{{r('logout', ['success'=>r('home')])}}}">Logout</a></li>
    @else
      <li><a href="{{{r('login', Route::currentRouteName()=='login' ? [] : ['success'=>Request::url(), 'cancel'=>Request::url()])}}}">Log in</a></li>
    @endif
  </ul>
</div>
@if(Auth::user() && Auth::user()->has_messages && !Auth::user()->has_read_messages && Route::currentRouteName()!='inbox')
  <div class="alert alert-warning">
    You have important unread messages. <a href="{{{r('inbox')}}}">Check your inbox now.</a>
  </div>
@endif
@foreach(['success', 'warning', 'danger'] as $kind)
  @if(Session::get($kind))
    <div class="alert alert-{{{$kind}}}">
      {{{Session::get($kind)}}}
    </div>
  @endif
  <?php Session::forget($kind); ?>
@endforeach