@extends('app')

@section('head')
  <title>pick.cool</title>
@stop

@section('content')
  <div style="max-width: 320px; margin-left: auto; margin-right: auto">
    <h1 class="modal-title">Connect via Facebook</h1>
    <p>To vote in a contest, enter a contest, or view a private contest, you must connect via Facebook.</p>
    <p style="text-align: center">
      <a class="btn btn-primary btn-xl" href="{{{route('facebook.authorize')}}}" ><i class="fa fa-facebook-square"></i> Connect via Facebook</a>
    </p>
    <p class="small">Your privacy and the security of this voting platform is our priority. Please see our <a href="/privacy">privacy policy</a> for more information.
    </p>
  </div>
@stop