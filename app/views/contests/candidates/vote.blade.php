@extends('app')

@section('head')
<style>
.navbar { display: none;}
#userheader { display: none;}
body
{
  padding-top: 10px;
}
img
{
  width: 100%;
  border-radius: 3px;
  border: 1px solid gray;
  margin-bottom: 10px;
}
form
{
  width: 320px;
  margin-left: auto;
  margin-right: auto;
}
form.hidden-xs
{
  width: 100%;
}
h1
{
  text-align: center;
  font-size: 24px;
}
</style>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
function submit()
{
  document.getElementById("vote").submit();
}
</script>
@stop


@section('content')
  <h1>Pat down! Are you a robot?</h1>
  @if(Auth::user()->current_vote_for($contest))
    <div class='alert alert-danger'>
      <b>Warning:</b> You have already voted in this pick. If you change your vote now, you will lose
      your leaderboard points accumulated in this pick. See <a href="{{{route('leaderboard')}}}">the leaderboard</a> for game details.
    </div>
  @endif
  <div class="row">
    <div class="col-xs-4">
      <img src="{{{$candidate->image_url}}}"/>
    </div>
    <div class="col-xs-8">
      To vote for <b>{{{$candidate->name}}}</b>, we gotta know you're no bot!
      <form action="{{{$candidate->vote_url}}}" method="get" class="hidden-xs" id="vote">
        <div class="g-recaptcha" data-sitekey="6Lcn7g0TAAAAAH6XjGkrCezSukFPykSlX0Zq4Xoo" data-callback="submit"></div>
      </form>
    </div>
  </div>
  <form action="{{{$candidate->vote_url}}}" method="get" class="visible-xs-block" id="vote">
    <div class="g-recaptcha" data-sitekey="6Lcn7g0TAAAAAH6XjGkrCezSukFPykSlX0Zq4Xoo" data-callback="submit"></div>
  </form>
@stop
