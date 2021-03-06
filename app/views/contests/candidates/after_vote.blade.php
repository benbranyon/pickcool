@extends('contests.candidates.layout')



@section('contests.candidates.content')
  <div class="row">
    @if(Input::get('v')=='new')
      <div class="col-xs-12">
        <p style="text-align:center;">Awesome sauce, you voted for {{{$candidate->name}}}. To help even more, share this page with your friends.</p>
      </div>
    @endif
    <div class="col-xs-12 text-success" style="font-size: 19px;text-align:center;">
      <i class="fa fa-check"></i>
      @if(Input::get('v')=='new')
        You voted for <br/>{{$candidate->name}}
      @else
        @if(Input::get('v')=='changed')
          You changed your vote to <br/>{{$candidate->name}}
        @else
          You already voted for <br/>{{$candidate->name}}
        @endif
      @endif
    </div>
    <div class="col-xs-12">
      <div class="candidate-small" style="width:100%;height:auto;border:none;">
        <img style="width:auto;max-width:275px;height:auto;display:block;margin:0 auto;" src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
      </div>
    </div>
  </div>
  <div class="row">
  <p style="text-align: left;margin: 0 auto;max-width: 275px;margin-bottom: 10px;">You can only vote for ONE person, but you can change your vote any time.</p>
  @if ($contest->nextContest() != null)
    <div class="after-vote-share col-sm-6">
      <button class="btn btn-primary btn-lg btn-full" onclick="share()" style="max-width:275px;margin:0 auto;display:block;margin-bottom:10px"><i class="fa fa-facebook"></i> Share Now</button>
      <h2 style="text-align: left;max-width: 275px;margin: 0 auto;margin-bottom: 10px;">To vote is awesome, to share is divine. Help your pick win by sharing with friends.</h2>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-lg btn-success btn-full" style="display:block;max-width:275px;margin:0 auto;margin-bottom:10px;" href="{{{$contest->nextContest()->canonical_url}}}">Next Contest</a>
        <h2 style="text-align: left;max-width: 275px;margin: 0 auto;margin-bottom: 10px;">Now it is time to vote for {{ $contest->nextContest()->title }}.</h2>
    </div>
  @else
    <div class="after-vote-share col-sm-12">
      <button class="btn btn-primary btn-lg btn-full" onclick="share()" style="max-width:275px;margin:0 auto;display:block;margin-bottom:10px"><i class="fa fa-facebook"></i> Share Now</button>
      <h2 style="text-align: left;max-width: 275px;margin: 0 auto;margin-bottom: 10px;">To vote is awesome, to share is divine. Help your pick win by sharing with friends.</h2>
    </div>
  @endif
  </div>
    @if($contest->ticket_url)
      <div class="col-sm-12">
      <hr />
      <div style="text-align:center;">
        <a style="color:black;" href="http://www.janugget.com/entertainment/celebrity-showroom.html">Music, Models, and Ink Awards Ceremony. February 19.</a>
        <a href="http://www.janugget.com/entertainment/celebrity-showroom.html"><img style="max-width:150px;margin:0 auto;" alt="John Ascuaga's Nugget" class="img-responsive" src="/assets/img/nugget-color-logo.jpg" /></a>
      </div>
      <hr />     
      </div>
    @endif
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
