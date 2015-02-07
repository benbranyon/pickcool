@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Images</h1>
	Total: <?php echo $images->getTotal();?>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>Image</th>
				<th>Candidate</th>
				<th>Contest</th>
				<th>Music URL</th>
				<th>Youtube URL</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($images as $image):?>
				<tr>
					<td>
            <a href="{{{$image->url('original')}}}">
              <img style="max-width:300px;" src="{{{$image->url('mobile')}}}" />
            </a>
          </td>
          <td>
          	@if(isset($image->candidate->name))
          		{{{$image->candidate->name}}}
          	@endif
          </td>
          <td>
          	@if(isset($image->candidate->contest->title))
          		{{{$image->candidate->contest->title}}}
          	@endif
          </td>
          <td>
          	@if(isset($image->candidate->youtube_url))
          		{{{$image->candidate->youtube_url}}}
          	@endif
          </td>
          <td>
          	@if(isset($image->candidate->music_url))
          		{{{$image->candidate->music_url}}}
          	@endif
          </td>
          </td>
					<td>
						<a class="btn btn-sm btn-default" href="{{{r('admin.images.status', ['image_id'=>$image->id, 'status'=>'approved'])}}}"><i class="fa fa-check"></i> Approve Standard</a>
						<a class="btn btn-sm btn-success" href="{{{r('admin.images.status', ['image_id'=>$image->id, 'status'=>'featured'])}}}"><i class="fa fa-user"></i> Approve Featured</a>
						<a class="btn btn-sm btn-warning" href="{{{r('admin.images.status', ['image_id'=>$image->id, 'status'=>'adult'])}}}"><i class="fa fa-flag"></i> Approve 18+</a>
						<a class="btn btn-sm btn-danger" href="{{{r('admin.images.status', ['image_id'=>$image->id, 'status'=>'declined'])}}}"><i class="fa fa-ban"></i> Decline</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $images->links(); ?>
@stop