@extends('contests.layout')

@section('contests.content')
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
                  <div class="badges">
                    @if($candidate->is_on_fire)
                      <div>
                        <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i> </span> On fire
                      </div>
                    @endif
                    @if($candidate->is_giver)
                      <div>
                        <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span> Charitable Giver
                      </div>
                    @endif
                  </div>                  
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
                  <div class="badges">
                    @if($candidate->is_on_fire)
                      <div>
                        <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i> </span> On fire
                      </div>
                    @endif
                    @if($candidate->is_giver)
                      <div>
                        <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span> Charitable Giver
                      </div>
                    @endif
                  </div>                  
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
                  <div class="badges">
                    @if($candidate->is_on_fire)
                      <div>
                        <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i> </span> On fire
                      </div>
                    @endif
                    @if($candidate->is_giver)
                      <div>
                        <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span> Charitable Giver
                      </div>
                    @endif
                  </div>                  
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
                  <div class="badges">
                    @if($candidate->is_on_fire)
                      <div>
                        <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i> </span> On fire
                      </div>
                    @endif
                    @if($candidate->is_giver)
                      <div>
                        <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$candidate->charity_name}}}."><i class="fa fa-heart"></i></span> Charitable Giver
                      </div>
                    @endif
                  </div>                  
              </td>
            </tr>
          @endforeach
        </table>
      </div>
<script>
@if($contest->can_end && !$contest->is_ended)
  var refresh = 60;
  var realtime = function() {
    refresh = refresh - 1;
    if(refresh==0) window.location.reload();
    document.getElementById('realtime').innerHTML = refresh;
    setTimeout(realtime,1000);
  };
  realtime();
@endif
  
</script>

@stop
