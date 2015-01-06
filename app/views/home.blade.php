@extends('app')

@section('head')
<title>pick.cool</title>
<meta property="og:type" content="website" />
<meta property="og:title" content="pick.cool"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="http://pick.cool"/>
<meta property="og:description" content="Vote and watch social contests in real time."/>
<meta property="og:image" content="{{{$contests[0]->canonical_url}}}"/>
@stop

@section('content')
  <div class="list">
    @foreach($contests as $contest)
      <div class="contest">
        <span class="votes-total">
          {{{$contest->vote_count_0}}}
        </span>
        <a class="title" href="{{{$contest->canonical_url}}}">{{{$contest->title}}}</a>
        @if($contest->is_editable)
          <a class="btn btn-xs btn-success" href="route('contest.edit', [$contest->id])">Edit</a>
        @endif
        <div class="clearfix"></div>
        <ul class="list-inline" style="margin-left: 0px; margin-bottom: 15px">
          @foreach($contest->candidates->take(5) as $candidate)
            <li>
              <a class="candidate-small" href="{{{$contest->canonical_url}}}"  class="{{{$contest->current_user_candidate_id == $candidate->id ? 'selected' : '' }}}">
                <img src="/loading.gif" data-echo="{{{$candidate->image_url('thumb')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}">
                <span class='votes-count'>{{{$candidate->vote_count_0}}}</span>
              </a>
            </li>
          @endforeach
          @if($contest->candidates->count()>5)
            <li ><a href="{{{$contest->canonical_url}}}">...{{{$contest->candidates->count()-5}}} more</a></li>
          @endif
        </ul>
      </div>
    @endforeach
  </div>
@stop