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
				<th></th>
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