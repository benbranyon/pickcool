@extends('appadmin')

@section('head')

@stop

@section('content')

	<h1>Candidates</h1>
	Total: <?php echo $candidates->getTotal();?>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>Image</th>
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
					<td><img style="max-width:150px;" src="{{{$candidate->image_url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/></td>
					<td>{{{$candidate->name}}}</td>
					<td>{{{ isset($candidate->contest->title) ? $candidate->contest->title : 'Default' }}}</td>
					<td>{{{$candidate->vote_boost}}}</td>
					<td>{{{$candidate->charity_name}}}</td>
					<td>{{{$candidate->charity_url}}}</td>
					<td>{{{$candidate->dropped_at}}}</td>
					<td>
						<a class="btn btn-sm" href="{{Request::url()}}/<?php echo $candidate->id;?>/edit/">Edit</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $candidates->links(); ?>
@stop