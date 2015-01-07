@extends('app')

@section('head')
<title>{{{$contest->title}}} | pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="Vote in {{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
<meta property="og:description" content="{{{preg_replace("/\n/","&nbsp;&nbsp;", strip_tags(Markdown::render($contest->description)))}}}"/>
<meta property="og:image" content="{{{$contest->current_winner->image_url('facebook')}}}?_c={{microtime(true)}}"/>
@stop

@section('content')
<div class="view">
  <div class="contest">
    <h1>
      {{{$contest->title}}}
    </h1>
    <div style="width:320px; margin-left: auto; margin-right: auto; text-align: center">
      <a href="{{{$contest->canonical_url}}}?s=s">View Small</a>
      |
      <a href="{{{$contest->canonical_url}}}?s=l">View Large</a>
      |
      <a href="{{{$contest->realtime_url}}}">Realtime</a>
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
            var refresh = 60;
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
              refresh = refresh - 1;
              if(refresh==0) window.location.reload();
              document.getElementById('realtime').innerHTML = refresh;
              setTimeout(update,1000);
            };
          </script>
        </div>
      @endif

      <p>
        <b><i>This page will refresh in <span id="realtime"></span> seconds.</i></b>
      </p>
      <?php
      function hinterval($interval)
      {
        if($interval == 1) return "$interval hour";
        if($interval < 24) return "$interval hours";
        $interval = (int)($interval/24);
        if($interval == 1) return "$interval day";
        return "$interval days";
      }
      function mlink($interval)
      {
        if(Input::get('h',12)==$interval) return hinterval($interval);
        return "<a href='?h={$interval}'>".hinterval($interval)."</a>";
      }
      ?>
      <p>
        {{mlink(1)}} |
        {{mlink(12)}} |
        {{mlink(24)}} |
        {{mlink(72)}} |
        {{mlink(120)}} |
        {{mlink(240)}}
      </p>
      
      <style>
      .standing
      {
        font-size: 20px;
        position: relative;
        top: 25px;
      }
      </style>
      <div style="max-width: 500px; margin-left: auto; margin-right: auto;">
        <?php
        $interval = Input::get("h",12);
        $rank_key = "rank_{$interval}";
        $vote_key = "vote_count_{$interval}";
        $candidates = $contest->ranked_candidates($interval);
          
        ?>
        
        <?php
          $top = $candidates
            ->filter(function($obj) use($interval) { return $obj->vote_change_since($interval)>0; })
            ->sort(function($a, $b) use($interval) {
              $diff = $b->vote_change_since($interval) - $a->vote_change_since($interval);
              if($diff!=0) return $diff;
              return $a->rank_0 - $b->rank_0;
            })
            ->take(10);
        ?>
        <table class="table table-bordered">
          <tr>
            <td colspan=2>
              <h1>Top 10 Movers by Votes Gained</h1>
            </td>
          </tr>
          <tr>
            <td></td>
            <th>Name</th>
          </tr>
          @foreach($top as $candidate)
            <?php
            $vote_change = $candidate->vote_change_since($interval);
            $rank_change = $candidate->rank_change_since($interval);
            ?>
            <tr>
              <td>
                <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
                  <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}"/ alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                </a>
              </td>
              <td style="text-align: left">
                <a href="{{{$candidate->canonical_url($contest)}}}">{{{$candidate->name}}}</a>
                <br/>
                Standing: #{{{$candidate->rank_0}}}
                  @if($rank_change > 0)
                    (<span style="color: green"><i class="fa fa-arrow-up"></i>{{{$rank_change}}}</span>)
                  @endif
                  @if($rank_change < 0)
                    (<span style="color: red"><i class="fa fa-arrow-down"></i>{{{abs($rank_change)}}}</span>)
                  @endif
                <br/>
                Votes: {{{$candidate->vote_count_0}}}
                  @if($vote_change > 0)
                    (<span style="color: green">+{{{$vote_change}}}</span>)
                  @endif
                  @if($vote_change < 0)
                    (<span style="color: red">-{{{abs($vote_change)}}}</span>)
                  @endif
                  @if($candidate->is_on_fire)
                    <br/>
                    <span class="fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span> On fire
                  @endif
                
              </td>
            </tr>
          @endforeach

          <?php
            $top = $candidates
              ->filter(function($obj) use($interval) { return $obj->rank_change_since($interval)>0; })
              ->sort(function($a, $b) use($interval) {
                $diff = $b->rank_change_since($interval) - $a->rank_change_since($interval);
                if($diff!=0) return $diff;
                return $a->rank_0 - $b->rank_0;
              })
              ->take(10);
          ?>
          <tr>
            <td colspan=2>
              <h1>Top 10 Movers by Change in Standing</h1>
            </td>
          </tr>
          <tr>
            <td></td>
            <th>Name</th>
          </tr>
          @foreach($top as $candidate)
            <?php
            $vote_change = $candidate->vote_change_since($interval);
            $rank_change = $candidate->rank_change_since($interval);
            ?>
            <tr>
              <td>
                <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
                  <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}"/ alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                </a>
              </td>
              <td style="text-align: left">
                <a href="{{{$candidate->canonical_url($contest)}}}">{{{$candidate->name}}}</a>
                <br/>
                Standing: #{{{$candidate->rank_0}}}
                  @if($rank_change > 0)
                    (<span style="color: green"><i class="fa fa-arrow-up"></i>{{{$rank_change}}}</span>)
                  @endif
                  @if($rank_change < 0)
                    (<span style="color: red"><i class="fa fa-arrow-down"></i>{{{abs($rank_change)}}}</span>)
                  @endif
                <br/>
                Votes: {{{$candidate->vote_count_0}}}
                  @if($vote_change > 0)
                    (<span style="color: green">+{{{$vote_change}}}</span>)
                  @endif
                  @if($vote_change < 0)
                    (<span style="color: red">-{{{abs($vote_change)}}}</span>)
                  @endif
                  @if($candidate->is_on_fire)
                    <br/>
                    <span class="fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span> On fire
                  @endif
                
              </td>
            </tr>
          @endforeach
          
          
          
          <?php
            $top = $candidates
              ->filter(function($obj) use($interval) { return $obj->created_at->diffInHours()<=$interval; })
              ->sort(function($a, $b) use($interval) {
                return $a->rank_0 - $b->rank_0;
              })
              ;
          ?>
          <tr>
            <td colspan=2>
              <h1>New</h1>
            </td>
          </tr>
          <tr>
            <td></td>
            <th>Name</th>
          </tr>
          @foreach($top as $candidate)
            <?php
            $vote_change = $candidate->vote_change_since($interval);
            $rank_change = $candidate->rank_change_since($interval);
            ?>
            <tr>
              <td>
                <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
                  <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}"/ alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                </a>
              </td>
              <td style="text-align: left">
                <a href="{{{$candidate->canonical_url($contest)}}}">{{{$candidate->name}}}</a>
                <br/>
                Standing: #{{{$candidate->rank_0}}}
                  @if($rank_change > 0)
                    (<span style="color: green"><i class="fa fa-arrow-up"></i>{{{$rank_change}}}</span>)
                  @endif
                  @if($rank_change < 0)
                    (<span style="color: red"><i class="fa fa-arrow-down"></i>{{{abs($rank_change)}}}</span>)
                  @endif
                <br/>
                Votes: {{{$candidate->vote_count_0}}}
                  @if($vote_change > 0)
                    (<span style="color: green">+{{{$vote_change}}}</span>)
                  @endif
                  @if($vote_change < 0)
                    (<span style="color: red">-{{{abs($vote_change)}}}</span>)
                  @endif
                  @if($candidate->is_on_fire)
                    <br/>
                    <span class="fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span> On fire
                  @endif
                
              </td>
            </tr>
          @endforeach
          
          
          
          
          <?php
            $top = $candidates
              ->sort(function($a, $b) use($interval) {
                return $a->rank_0 - $b->rank_0;
              })
          ?>
          <tr>
            <td colspan=2>
              <h1>All Candidates by Rank</h1>
            </td>
          </tr>
          <tr>
            <td></td>
            <th>Name</th>
          </tr>
          @foreach($top as $candidate)
            <?php
            $vote_change = $candidate->vote_change_since($interval);
            $rank_change = $candidate->rank_change_since($interval);
            ?>
            <tr>
              <td>
                <a class="candidate-small {{ $candidate->is_user_vote ? 'selected' : ''}}" href="{{{$candidate->canonical_url($contest)}}}"  >
                  <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}"/ alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                </a>
              </td>
              <td style="text-align: left">
                <a href="{{{$candidate->canonical_url($contest)}}}">{{{$candidate->name}}}</a>
                <br/>
                Standing: #{{{$candidate->rank_0}}}
                  @if($rank_change > 0)
                    (<span style="color: green"><i class="fa fa-arrow-up"></i>{{{$rank_change}}}</span>)
                  @endif
                  @if($rank_change < 0)
                    (<span style="color: red"><i class="fa fa-arrow-down"></i>{{{abs($rank_change)}}}</span>)
                  @endif
                <br/>
                Votes: {{{$candidate->vote_count_0}}}
                  @if($vote_change > 0)
                    (<span style="color: green">+{{{$vote_change}}}</span>)
                  @endif
                  @if($vote_change < 0)
                    (<span style="color: red">-{{{abs($vote_change)}}}</span>)
                  @endif
                  @if($candidate->is_on_fire)
                    <br/>
                    <span class="fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span> On fire
                  @endif
                
              </td>
            </tr>
          @endforeach
        </table>
      </div>
      
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
                <a href="{{{$sponsor->url}}}" target="_self"><img src="{{{$sponsor->image_url('thumb')}}}" align="left" /></a>
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
@if($contest->can_end && !$contest->is_ended)
  update();
@endif
function share(url)
{
  FB.ui({
    method: 'share',
    href: url,
  });
}
</script>

@stop
