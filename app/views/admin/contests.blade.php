@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Contests</h1>
	Total: <?php echo $contests->getTotal();?>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th class="col-sm-2">Title</th>
				<th class="col-sm-4">Description</th>
				<th class="col-sm-1">Write-In</th>
				<th class="col-sm-2">Password</th>
				<th class="col-sm-1">Ends</th>
				<th class="col-sm-2">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($contests as $contest):?>
				<tr>
					<td>{{{$contest->title}}}</td>
					<td>{{{$contest->description}}}</td>
					<td>{{{$contest->writein_enabled}}}</td>
					<td>{{{$contest->password}}}</td>
					<td>{{{$contest->ends_at}}}</td>
					<td>
						<a class="btn btn-sm" href="{{Request::url()}}/<?php echo $contest->id;?>/edit/">Edit</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $contests->links(); ?>
@stop