<div class="row contest">
  <div class="col-xs-2 col-sm-3 ">
    <img src="/loading.gif" data-echo="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}"/>
  </div>
  <div class="col-xs-7 col-sm-9 title">
    <a class="title" href="{{{$contest->canonical_url}}}">{{$contest->title}}</a>
    <br/>
    <i><a href="{{{$candidate->canonical_url($contest)}}}">{{$candidate->name}} - {{$candidate->vote_count_0}} votes</a></i>
    <div class="hidden-xs">
      @if($user->is_in_contest($contest))
        <span class="text-warning"><i>in pick</i></span>
      @else
        <span class="text-{{$style}}">
          @if($status=='pending')
            <i class="fa fa-arrow-up"></i>{{$user->pending_points_for($contest)}}
          @else
            {{$user->pending_points_for($contest)}} 
          @endif
          {{$status}}
        </span>
      @endif
    </div>
  </div>
  <div class="col-xs-3 votes visible-xs-block">
    @if($user->is_in_contest($contest))
      <span class="text-warning"><i>in pick</i></span>
    @else
      <span class="text-{{$style}}">
        <span class="text-{{$style}}">
          @if($status=='pending')
            <i class="fa fa-arrow-up"></i>{{$user->pending_points_for($contest)}}
          @else
            {{$user->pending_points_for($contest)}} 
          @endif
          {{$status}}
        </span>
      </span>
    @endif
  </div>
</div>
