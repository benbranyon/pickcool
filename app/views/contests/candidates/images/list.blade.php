@extends('contests.candidates.layout')

@section('contests.candidates.content')
  @foreach($candidate->images as $image)
    <?php if(!$image->screened_at && !$candidate->is_owner) continue; ?>
    <div class="clearfix" style="margin: 5px; margin-bottom: 20px;
border: 1px solid rgb(219, 219, 219);
border-radius: 5px;
padding: 9px;">
      <div id="candidate" class="candidate-large">
        <a href="{{{$image->url('facebook')}}}">
          <img src="{{{$image->url('mobile')}}}" alt="{{{$candidate->name}}}" />
        </a>
      </div>
      <div class="buttons">
        <div class="pull-left">
          @if($image->screened_at)
            @if($image->status=='approved')
              <span class="text-success"><i class="fa fa-check"></i> Approved Standard</span>
            @endif
            @if($image->status=="featured")
              <span class="text-success"><i class="fa fa-user"></i> Approved Featured</span>
            @endif
            @if($image->status=="adult")
              <span class="text-success"><i class="fa fa-flag"></i> Approved 18+</span>
            @endif
            @if($image->status=="declined")
              <span class="text-danger"><i class="fa fa-ban"></i> Declined</span>
            @endif
          @else
            <span class="text-warning"><i class="fa fa-eye"></i> In Review</span>
          @endif
        </div>
        <div class="pull-right">
          @if($image->status=='featured')
            <a href="" class="btn btn-xl btn-success"><i class='fa fa-user'></i> Make Featured</a>
          @endif
          <a href="" class="btn btn-xl btn-danger"><i class='fa fa-close'></i> Delete</a>
        </div>
      </div>
    </div>
  @endforeach
  @if($candidate->is_owner)
    <div class="clearfix">
      <div class="pull-right">
        <a href="{{{$candidate->add_image_url}}}" class="btn btn-xl btn-primary"><i class="fa fa-plus"></i> Add Image</a>
      </div>
    </div>
  @endif

@stop
