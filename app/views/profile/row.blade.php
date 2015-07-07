<div class="row contest">
  <div class="col-xs-2 col-sm-3 ">
    <img src="/loading.gif" data-echo="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}"/>
  </div>
  <div class="col-xs-7 col-sm-9 title">
    <a class="title" href="{{{$contest->canonical_url}}}">{{$contest->title}}</a>
    <br/>
    <i><a href="{{{$candidate->canonical_url($contest)}}}">{{$candidate->name}} - {{$candidate->vote_count_0}} votes</a></i>
    <div class="hidden-xs">
      <span class="text-{{$style}}">+{{$user->pending_points_for($contest)}} {{$status}}</span>
    </div>
  </div>
  <div class="col-xs-3 votes visible-xs-block"><span class="text-{{$style}}">+{{$user->pending_points_for($contest)}} {{$status}}</span></div>
</div>
