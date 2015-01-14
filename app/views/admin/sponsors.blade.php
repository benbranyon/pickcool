@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Sponsors</h1>
	Total: <?php echo $sponsors->getTotal();?>
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
					<td><img alt="{{{$sponsor->name}}}" src="#" align="left" /></td>
					<td><?php echo $sponsor->name;?></td>
					<td><?php echo $sponsor->description;?></td>
					<td><?php echo $sponsor->url;?></td>
					<td>
						<a class="btn btn-sm" href="#">Edit</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $sponsors->links(); ?>
@stop