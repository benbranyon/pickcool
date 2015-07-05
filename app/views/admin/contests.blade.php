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
				<th class="col-sm-1">Write-In</th>
				<th class="col-sm-2">Password</th>
				<th class="col-sm-1">Ends</th>
				<th class="col-sm-1">State</th>
				<th class="col-sm-1">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($contests as $contest):?>
				<tr class='<?php echo($contest->is_archived ? 'archived' : '')?>'>
					<td>{{{$contest->title}}}</td>
					<td>{{{$contest->writein_enabled}}}</td>
					<td>{{{$contest->password}}}</td>
					<td>{{{$contest->ends_at}}}</td>
					<td>{{{$contest->state}}}</td>
					<td>
						<a class="btn btn-sm" href="{{Request::url()}}/<?php echo $contest->id;?>/edit/">Edit</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $contests->links(); ?>
@stop