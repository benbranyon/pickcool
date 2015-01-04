@extends('app')

@section('head')
<title>{{{$contest->title}}} | pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{route('image.view', [$contest->current_winner->image_id, 'facebook'])}}}?_c={{microtime(true)}}"/>
@stop

@section('content')
<div class="view">
  <div class="contest">
    <h1>
      {{{$contest->title}}}
    </h1>
    <div style="width:320px; margin-left: auto; margin-right: auto; text-align: center">
      <a href="?s=s">View Small</a>
      |
      <a href="?s=l">View Large</a>
    </div>
    <div> 
      @if($contest->is_ended)
        <div class="text-danger" ng-if="$contest->is_ended">
          Voting has ended.
        </div>      
      @endif
      
      @if($contest->can_end && !$contest->is_ended)
        <div class="countdown">
          <h2>
            {{{$contest->vote_count}}} votes | 
            {{{$contest->candidates->count()}}} picks | 
            @if($contest->writein_enabled)
              <span class="text-success">OPEN pick - Join below</span>
            @endif
          </h2>
          <table class="countdown table">
            <tr class="times">
              <td>Ends</td>
              <td><span id='days'></span></td>
              <td><span id='hours'></span></td>
              <td><span id='minutes'></span></td>
              <td class="seconds"><span id='seconds'></span></td>
            </tr>
            <tr class="labels">
              <td></td>
              <td>Days</td>
              <td>Hours</td>
              <td>Minutes</td>
              <td>Seconds</td>
            </tr>
          </table>
          <script>
            var update = function() {
              var now = new Date() / 1000;
              var dd = Math.max(0,{{$contest->ends_at->format('U')}} - now);
              var duration = {
                days: Math.floor(dd/(60*60*24)*1),
                hours: Math.floor((dd%(60*60*24))/(60*60)*1),
                minutes: Math.floor(((dd%(60*60*24))%(60*60))/(60)*1),
                seconds: Math.floor((((dd%(60*60*24))%(60*60))%(60))*1),
              };
              for(var k in duration)
              {
                var text = "00".substring(0,2-(""+duration[k]).length)+duration[k];
                document.getElementById(k).innerHTML = text;
              }
              setTimeout(update,1000);
            };
            update();
          </script>
        </div>
      @endif

      @if($contest->can_vote)
        <p>
          <b><i>Choose your favorite pick below and cast your vote.</i></b>
        </p>
      @endif
      
      <ul class="list-inline clearfix" style="text-align: center">
        @if($contest->can_join && $contest->password)
          <li>
            @if(Session::get('contest_view_mode','s')=='s')
              <a class="candidate-small" href="{{{$contest->join_url}}}">
                <img src="/add-user.png"/ alt="You" title="Join the Pick" />
                <div class="overlay">
                  <div class="btn btn-xs btn-success">Join Now!</div>
                </div>
              </a>
            @else
              <div >
                <a class="candidate-large" href="{{{$contest->join_url}}}">
                  <img src="/add-user.png"/ alt="You" title="Join the Pick" />
                  <div class="overlay">
                    <div class="btn btn-lg btn-success">Join Now!</div>
                  </div>
                </a>
              </div>
            @endif
          </li>
        @endif
        
        @foreach($contest->candidates as $candidate)
          <li >
            @if(Session::get('contest_view_mode','s')=='s')
              <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url}}}"  >
                <img src="/loading.gif" data-echo="/images/{{$candidate->image_id}}/thumb"/ alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                <span class='votes-count'>{{{$candidate->vote_count}}}</span>
              </a>
            @else
              <div >
                <h1 class="big">{{{$candidate->name}}} ({{{$candidate->vote_count}}} votes)</h1>
                <a class="candidate-large {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url}}}"  >
                  <img src="/loading.gif" data-echo="/images/{{$candidate->image_id}}/mobile"/ alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                </a>
                <a class="btn btn-md btn-primary btn-half" href="{{{$candidate->canonical_url}}}"><i class="fa fa-camera"></i> More</a>
                @if($candidate->is_user_vote)
                  <a class="btn btn-md btn-warning btn-half" href="{{{$candidate->unvote_url}}}"><i class="fa fa-close"></i> Unvote</a>
                @else
                  <a class="btn btn-md btn-primary btn-half" href="{{{$candidate->vote_url}}}"><i class="fa fa-check"></i> Vote</a>
                @endif
                <button class="btn btn-md btn-primary btn-half" onclick="share({{{json_encode($candidate->canonical_url)}}})"><i class="fa fa-facebook"></i> Share</button>
                <hr/>
              </div>
            @endif
          </li>
        @endforeach
        
        @if($contest->can_join)
          <li>
            @if(Session::get('contest_view_mode','s')=='s')
              <a class="candidate-small" href="{{{$contest->join_url}}}">
                <img src="/add-user.png"/ alt="You" title="Join the Pick" />
                <div class="overlay">
                  <div class="btn btn-xs btn-success">Join Now!</div>
                </div>
              </a>
            @else
              <div >
                <h1 class="big">You</h1>
                <a class="candidate-large" href="{{{$contest->join_url}}}">
                  <img src="/add-user.png"/ alt="You" title="Join the Pick" />
                  <div class="overlay">
                    <div class="btn btn-lg btn-success">Join Now!</div>
                  </div>
                </a>
                <button disabled=true class="btn btn-md btn-primary btn-half"><i class="fa fa-camera"></i> More</button>
                <button disabled=true a class="btn btn-md btn-primary btn-half" ><i class="fa fa-camera"></i> More</button>
                <button disabled=true  class="btn btn-md btn-primary btn-half"><i class="fa fa-camera"></i> More</button>
              </div>
            @endif
          </li>
        @endif
      </ul>
      
      @if($contest->description)
        <div >
          <h2>About this Pick</h2>
          <div class="description">
            {{Markdown::render($contest->description)}}
          </div>
        </div>
      @endif


      @if($contest->sponsors->count()>0)
        <div >
          <h2>Pick Sponsors</h2>
          <div class="sponsors">
            @foreach($contest->sponsors as $sponsor)
              <div class="sponsor clearfix">
                <a href="{{{$sponsor->url}}}" target="_self"><img src="/images/{{$sponsor->image_id}}/thumb" align="left" /></a>
                <a href="{{{$sponsor->url}}}" target="_self">{{{$sponsor->name}}}</a>
                <br/>
                {{{$sponsor->description}}}
              </div>
            @endforeach
          </div>
        </div>
      @endif
      
      
      @if($contest->is_editable)
        <a class="btn btn-xs btn-success" href="{{route('contests.edit', [$contest->id])}}">Edit</a>
      @endif
    

      @if($contest->is_user_in)
        <div class="alert alert-success">
          <h1>You are in this pick!</h1>
          <p>Your job now is to share, share, share, and encourage others to do so too. Here are some ideas that were successful for other users:</p>
          <ul>
            <li>Vote for someone. If not for yourself, then someone else in the pick.
            <li>Share your link to everyone in the world.
            <li>Not just once! Share every day.
            <li>Ask others to share your link too.
            <li>Make videos and other entertaining posts to raise awareness.
            <li>Be a good sport. Promote others too, you'd be surprised what that can do for your own standing.
          </ul>
          <div class="fb-share-button"  data-layout="button_count"></div>
        </div>
      @endif

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Discussion</h3>
        </div>
        <div class="panel-body" style="padding-left: 0px; padding-right: 0px;padding-top:0px">
          <div class="fb-comments" data-href="{{{$contest->canonical_url}}}" data-numposts="5" data-colorscheme="light" data-width="100%"></div>
        </div>
      </div>          
    </div>
  </div>
</div>
<script>
function share(url)
{
  FB.ui({
    method: 'share',
    href: url,
  });
}
</script>

@stop
