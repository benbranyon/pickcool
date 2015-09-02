@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Sponsors</h1>
	<p>Total: <?php echo $sponsors->getTotal();?></p>

	<a href="{{Request::url()}}/add">Add Sponsor</a>

	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>Image</th>
				<th>Name</th>
				<th>Description</th>
				<th>Url</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($sponsors as $sponsor):?>
				<tr>
					<td>
						@if (isset($sponsor->image_id))
							<img alt="{{{$sponsor->name}}}" src="{{{$sponsor->image_url('thumb')}}}" />
						@endif
					</td>
					<td>{{{$sponsor->name}}}</td>
					<td>{{{$sponsor->description}}}</td>
					<td>{{{$sponsor->url}}}</td>
					<td>
						<a class="btn btn-sm" href="{{Request::url()}}/{{{$sponsor->id}}}/edit/">Edit</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $sponsors->links(); ?>
@stop