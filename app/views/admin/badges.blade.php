@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Badges</h1>
	Total: <?php echo $badges->getTotal();?>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>Candidate Name</th>
				<th>Contest Title</th>
				<th>Name</th>
				<th>Vote Weight</th>
				<th>Created</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($badges as $badge):?>
				<tr>
					<td>{{{$badge->candidate->name}}}</td>
					<td>{{{$badge->contest->title}}}</td>
					<td>{{{$badge->name}}}</td>
					<td>{{{$badge->vote_weight}}}</td>
					<td>{{{$badge->created_at}}}</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $badges->links(); ?>
@stop