@extends('app')

@section('head')
<title>{{{$contest->title}}} | pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{$contest->current_winner->image_url('facebook')}}}?_c={{microtime(true)}}"/>
<script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
@stop

@section('foot')
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
@stop

@section('content')
<div class="view">
  <div class="contest">
    <h1>
      {{{$contest->title}}}
    </h1>
    @if($contest->sponsors->count()>0)
      <?php $sponsor = $contest->random_sponsor; ?>
      <h2>Sponsored by: <a href="{{{$sponsor->url}}}" target="_self">{{{$sponsor->name}}}</a></h2>
    @endif
    
    @if($contest->has_joined)
      <div class="alert alert-success" style="max-width: 600px; margin-left: auto; margin-right: auto; ">
        You are in this pick! Read about <a href="/tips">how to win</a>.
      </div>
    @endif
    <div style="width:100%; margin-left: auto; margin-right: auto; text-align: center">
      <a href="{{{$contest->canonical_url}}}?s=s">View Small</a>
      |
      <a href="{{{$contest->canonical_url}}}?s=l">View Large</a>
      |
      <a href="{{{$contest->realtime_url}}}">Realtime</a>
    </div>
    @if($contest->total_charity_hours>0)
      <div style="width:100%; margin-left: auto; margin-right: auto; text-align: center">
        <a href="?f=g"><i class="fa fa-heart"></i> View Givers</a>
        @if($contest->total_charity_dollars>0)
          | ${{{$contest->total_charity_dollars}}} pledged
        @endif
        @if($contest->total_charity_hours>0)
          | {{{$contest->total_charity_hours}}} volunteer hours pledged
        @endif
      </div>
    @endif
    <div> 
      @if($contest->is_ended)
        <div class="text-danger" ng-if="$contest->is_ended">
          Voting has ended.
        </div>      
      @endif
      
      @if($contest->can_end && !$contest->is_ended)
        <div class="countdown">
          <h2>
            {{{$contest->vote_count_0}}} votes | 
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

      @yield('contests.content')
      
      <div style="max-width: 600px; margin-left: auto; margin-right: auto; text-align: left">
        <div class="panel-group faq-accordion" id="accordion">
          @if($contest->description)
            <div class="panel panel-default">
                <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#description">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            About this Pick
                        </h4>
                    </div>
                </a>
                <div id="description" class="panel-collapse collapse">
                    <div class="panel-body">
                      {{Markdown::render($contest->description)}}
                    </div>
                </div>
            </div>
          @endif
          @if($contest->prizes)
            <div class="panel panel-default">
                <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#prizes">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Prizes
                        </h4>
                    </div>
                </a>
                <div id="prizes" class="panel-collapse collapse">
                    <div class="panel-body">
                        {{Markdown::render($contest->prizes)}}
                    </div>
                </div>
            </div>
          @endif
          @if($contest->rules)
            <div class="panel panel-default">
                <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#rules">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Rules
                        </h4>
                    </div>
                </a>
                <div id="rules" class="panel-collapse collapse">
                    <div class="panel-body">
                        {{Markdown::render($contest->rules)}}
                    </div>
                </div>
            </div>
          @endif
        </div>
      </div>
      



      @if($contest->sponsors->count()>0)
        <div >
          <h2>Pick Sponsors</h2>
          <div class="sponsors">
            @foreach($contest->sponsors as $sponsor)
              <div class="sponsor clearfix">
                <a href="{{{$sponsor->url}}}" target="_self"><img alt="{{{$sponsor->name}}}" src="{{{$sponsor->image_url('thumb')}}}" align="left" /></a>
                <a href="{{{$sponsor->url}}}" target="_self">{{{$sponsor->name}}}</a>
                <br/>
                {{{$sponsor->description}}}
              </div>
            @endforeach
            @if(Auth::check())
              <div class="text-center">
                <a href="/sponsor/signup/{{$contest->id}}" class="btn btn-lg btn-primary">Sponsor Signup</a>
              </div>
            @endif
          </div>
        </div>
      @endif
      
      
      @if($contest->is_editable)
        <a class="btn btn-xs btn-success" href="{{r('contests.edit', [$contest->id])}}">Edit</a>
      @endif
    </div>
  </div>
</div>
@stop
