@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Candidates</h1>
	Total: <?php echo $candidates->getTotal();?>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>Name</th>
				<th>Contest</th>
				<th>Vote Boost</th>
				<th>Charity Name</th>
				<th>Charity Url</th>
				<th>Dropped At</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($candidates as $candidate):?>
				<tr>
					<td><?php echo $candidate->name;?></td>
					<td><?php echo $candidate->contest->title;?></td>
					<td><?php echo $candidate->vote_boost;?></td>
					<td><?php echo $candidate->charity_name;?></td>
					<td><?php echo $candidate->charity_url;?></td>
					<td><?php echo $candidate->dropped_at;?></td>
					<td>
						<a class="btn btn-sm" href="{{Request::url()}}/<?php echo $candidate->id;?>/edit/">Edit</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $candidates->links(); ?>
@stop