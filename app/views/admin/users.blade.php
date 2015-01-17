@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Users</h1>
	Total: <?php echo $users->getTotal();?>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Gender</th>
				<th>Email</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users as $user):?>
				<tr>
					<td>{{{$user->first_name}}}</td>
					<td>{{{$user->last_name}}}</td>
					<td>{{{$user->gender}}}</td>
					<td>{{{$user->email}}}</td>
					<td><a class="btn btn-small" href="#">Edit</a></td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $users->links(); ?>
@stop