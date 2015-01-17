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
					<td><img style="max-width:150px;" src="{{{$image->url('thumb')}}}" /></td>
					<td>
						<a class="btn btn-sm btn-success" href="?a={{{$image->id}}}">Approve</a>
						<a class="btn btn-sm btn-danger" href="?d={{{$image->id}}}">Decline</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $images->links(); ?>
@stop