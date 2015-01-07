@extends('app')

@section('head')
  <title>pick.cool</title>
@stop

@section('content')
  <div style="max-width: 320px; margin-left: auto; margin-right: auto">
    <h1 class="modal-title">Connect via Facebook</h1>
    <p>To vote in a contest, enter a contest, or view a private contest, you must connect via Facebook.</p>
    <p><span class='text-danger'>Please authorize all the permissions requested.</span> <button class="btn btn-link" onclick="why()">Why?</button></p>
    <p style="display:none" id='why'>The voting on this site is about as fair as it comes. One vote per person, period. The trustworthiness of the voting system is the most important thing to us. We want everybody to shine and have the best chance at winning. To do that, we must be able to verify that each vote is from a real person. We use your information to do that, and to alert you of important events or updates to the pick. Thank you for supporting your pick, it means a lot to them and to us.</p>
    <script>
      var why = function()
      {
        document.getElementById('why').style.display = 'block';
      };
    </script>
    <p style="text-align: center">
      <a class="btn btn-primary btn-xl" href="{{{route('facebook.authorize')}}}" ><i class="fa fa-facebook-square"></i> Connect via Facebook</a>
    </p>
    <p class="small">Your privacy and the security of this voting platform is our priority. Please see our <a href="/privacy">privacy policy</a> for more information.
    </p>
  </div>
@stop