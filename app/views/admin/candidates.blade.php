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
					<td>
						@if(isset($candidate->image_id))
							<img style="max-width:150px;" src="{{{$candidate->image_url('thumb')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
						@endif	
					</td>
					<td>{{{$candidate->name}}}</td>
					<td>{{{ isset($candidate->contest->title) ? $candidate->contest->title : 'Default' }}}</td>
					<td>{{{$candidate->vote_boost}}}</td>
					<td>{{{$candidate->charity_name}}}</td>
					<td>{{{$candidate->charity_url}}}</td>
					<td>{{{$candidate->dropped_at}}}</td>
					<td>
						<a class="btn btn-sm" href="{{Request::url()}}/<?php echo $candidate->id;?>/edit/">Edit</a>
						<?php 
							$charity_badge = false;
							foreach($candidate->badges as $badge){
								if($badge->name = 'charity')
								{
									$charity_badge = true;
								}
							}
						?>
						@if($candidate->charity_url && $candidate->charity_name && !$charity_badge)<a href="/candidate/charity_boost/{{{$candidate->id}}}">Charity Boost</a>@endif
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>

	</table>
	<?php echo $candidates->links(); ?>
@stop