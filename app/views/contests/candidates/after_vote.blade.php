@extends('contests.candidates.layout')



@section('contests.candidates.content')
  <div class="row">
    <div class="col-xs-4">
      <div class="candidate-small">
        <img src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
      </div>
    </div>
    <div class="col-xs-8 text-success" style="font-size: 19px;">
      <i class="fa fa-check"></i>
      @if(Input::get('v')=='new')
        You voted for {{$candidate->name}}
      @else
        @if(Input::get('v')=='changed')
          You changed your vote to {{$candidate->name}}
        @else
          You already voted for {{$candidate->name}}
        @endif
      @endif
    </div>
  </div>
    @if($contest->ticket_url)
      <hr />
      <div style="text-align:center;">
        <a style="color:black;" href="http://www.janugget.com/entertainment/celebrity-showroom.html">Music, Models, and Ink Awards Ceremony. February 19.</a>
        <a href="http://www.janugget.com/entertainment/celebrity-showroom.html"><img style="max-width:150px;margin:0 auto;" alt="John Ascuaga's Nugget" class="img-responsive" src="/assets/img/nugget-color-logo.jpg" /></a>
      </div>
      <hr />     
    @endif
  <h1>To vote is awesome, to share is divine. Help your pick win by sharing with friends.</h1>
  <button class="btn btn-primary btn-lg btn-full" onclick="share()"><i class="fa fa-facebook"></i> Share Now</button>

  <div style="color: gray; font-size: 12px">
    <p>Awesome sauce, you voted for {{{$candidate->name}}}.</p>
    <p>You can only vote for ONE person, but you can change your vote any time.</p>
    <p>To help even more, share this page with your friends.</p>
  </div>
  <script>
    function share(response)
    {
      FB.ui({
        method: 'share',
        href: {{json_encode($candidate->canonical_url($contest))}},
      });
    }
  </script>

@stop
