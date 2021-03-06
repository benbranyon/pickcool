@extends('contests.candidates.layout')

@section('contests.candidates.content')
  <div class="candidate-single">
    @if(!$candidate->image_id && !$candidate->is_owner)
      <div class="alert alert-warning">
        Please note - the image for this voting profile is under review. No voting can take place until the image has been approved.
      </div>
    @else
      @if($candidate->is_owner)
        @if($candidate->has_pending_images && !$candidate->has_featured_image)
          <div class="alert alert-warning">
            @if($contest->category->name == 'Bands')
              Nobody can vote or share your profile yet because we are reviewing your image and music submission. Once we have approved your submission voting can begin.
            @else
              Nobody can vote or share your profile yet because you don't have a FEATURED image. We are still reviewing your images, so sit tight and <a href="{{{$candidate->add_image_url}}}">add more images</a> while you wait. Once we have approved a FEATURED image, voting can begin.
            @endif
          </div>
        @elseif($candidate->has_pending_images && $candidate->has_featured_image)
          <div class="alert alert-warning">
            We are reviewing the image(s) you submitted. This process should take no more than 24 hours.
          </div>
        @else
          @if(!$candidate->image_id)
            <div class="alert alert-danger">
              We reviewed all your images and none could be used as a featured image in this pick. Nobody can vote or share your profile yet because you do not have a FEATURED image. Please <a href="{{{$candidate->add_image_url}}}">add another one</a> and be sure to review the Featured Picture guidelines. 
            </div>
          @endif
        @endif
      @endif
      <?php
      $voters = $candidate->votes()->whereHas('user', function($q) { $q->where('is_visible', '=', 1); })->with('user')->orderBy('voted_at', 'desc');
      $voter_count = $voters->count();
      ?>
      @if($voter_count>0)
        <div class="voters">
          <?php foreach($voters->limit(5)->get() as $v): ?><a class="voter" href="{{$v->user->profile_url}}"><img class="profile-img" title="{{$v->user->full_name}}" src="{{$v->user->profile_image_url}}"/></a><?php endforeach; ?>
          <a href="{{$candidate->voters_url}}">
            @if($voter_count>5)
              ...and {{$voter_count-5}} more
            @endif
              voters publicly support {{$candidate->name}}.
          </a>
        </div>
      @endif
      @if($candidate->image_id)
        <div class="row">
          <div class="col-xs-12" style="text-align: center">
            @if($contest->is_voteable)
              @if($candidate->is_user_vote)
                <a class="btn btn-lg btn-warning btn-half" href="{{{$candidate->unvote_url}}}">Unvote</a>
              @else
                <a class="btn btn-lg btn-success btn-half" href="{{{$candidate->vote_url}}}"><i class="fa fa-check"></i> Vote</a>
              @endif
            @else
              @if($candidate->is_user_vote)
                <a class="btn btn-lg btn-warning btn-half" href="{{{$candidate->unvote_url}}}" disabled="disabled"><i class="fa fa-close"></i> Unvote</a>
              @else
                 <a class="btn btn-lg btn-primary btn-half" href="{{{$candidate->vote_url}}}" disabled="disabled"><i class="fa fa-check"></i> Vote</a>
              @endif
            @endif
            @if($contest->is_shareable)
              <a class="btn btn-lg btn-primary btn-half" onclick="share()"><i class="fa fa-facebook"></i> Share</a>
            @endif
            @if($contest->category->name == 'Bands')
              <a class="btn btn-md btn-primary btn-half discover" href="{{{$candidate->music_url}}}"><i class="fa fa-music"></i> Discover</a>
            @endif
          </div>
        </div>
      @endif
    
      <?php $idx=0;
      $images = $candidate->weighted_images;
      ?>
      @foreach($images as $image)
        <?php $idx++; ?>
        <?php if(!$image->screened_at && !$candidate->is_owner) continue; ?>
        <?php if($image->status == 'declined') continue;?>
        <?php $is_featured = ($candidate->image_id == $image->id); ?>
        @if($is_featured)
        <div class="clearfix"
        @else
        <div class="clearfix image-wrapper"
        @endif
          @if($candidate->is_owner)
            style="margin: 5px; margin-bottom: 20px;
              border: 1px solid rgb(219, 219, 219);
              border-radius: 5px;
              padding: 9px;"
          @endif
        >
          @if($is_featured && $candidate->is_owner)
            <h2>Featured</h2>
            <h2>(shows on pick and share pages)</h2>
          @endif
          @if($is_featured)
          <div class="candidate-large featured">
          @else
          <div class="candidate-large">
          @endif
            <a href="{{{$image->url('original')}}}">
              @if($is_featured)
                <img src="{{{$image->url('original')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
              @else
                <img src="{{{$image->url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
              @endif
            </a>
          </div>
          @if($candidate->is_owner)
            <div class="buttons">
              <div class="pull-left">
                @if($image->screened_at)
                  @if($image->status=='approved')
                    <span class="text-success"><i class="fa fa-check"></i> Approved Standard</span>
                  @endif
                  @if($image->status=="featured")
                    <span class="text-success"><i class="fa fa-user"></i> Approved Featured</span>
                  @endif
                  @if($image->status=="adult")
                    <span class="text-success"><i class="fa fa-flag"></i> Approved 18+</span>
                  @endif
                  @if($image->status=="declined")
                    <span class="text-danger"><i class="fa fa-ban"></i> Declined</span>
                  @endif
                @else
                  <span class="text-warning"><i class="fa fa-eye"></i> In Review</span>
                @endif
              </div>
              @if(!$contest->is_ended)
                <div class="pull-right">
                  @if(!$is_featured)
                    @if($idx>=3)
                      <a href="{{{$candidate->manage_url('moveup', $image->id)}}}" class="btn btn-default btn-xl"><i class="fa fa-arrow-up"></i></a>
                    @endif
                    @if($idx < count($images))
                      <a href="{{{$candidate->manage_url('movedown', $image->id)}}}" class="btn btn-default btn-xl"><i class="fa fa-arrow-down"></i></a>
                    @endif
                    @if($image->status=='featured')
                      <a href="{{{$candidate->manage_url('featured', $image->id)}}}" class="btn btn-xl btn-success"><i class='fa fa-user'></i> Feature</a>
                    @endif
                    <a href="{{{$candidate->manage_url('delete', $image->id)}}}" onclick="return confirm('Really delete this image?', 'Yes', 'No');" class="btn btn-xl btn-danger"><i class='fa fa-close'></i></a>
                  @endif
                </div>
              @endif
            </div>      
          @endif
      </div>
      @if($contest->category->name == 'Bands')
        @if(isset($candidate->bio) && $candidate->bio != '')
          <div class="bio">
            <strong>Bio:</strong> {{{$candidate->bio}}}
          </div>
          <br />
        @endif
        @if(isset($candidate->youtube_url) && $candidate->youtube_url != '')
          <div style=" position: relative;width:100%;height:0;padding-bottom:60%;">
            <?php preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$candidate->youtube_url,$matches);?>
            <?php if (isset($matches[1])):?>
              <iframe width="100%" height="100%" style="position: absolute;top: 0;left: 0;" src="https://www.youtube.com/embed/<?php echo $matches[1];?>" frameborder="0" allowfullscreen></iframe>
            <?php endif;?>
          </div>
          <br />
        @endif
      @endif
      @if($is_featured)
        <table class="table badges">
          @if($candidate->is_on_fire)
            <tr>
              <td>
                <span class="badge badge-fire" title="On fire! Gained {{{Candidate::$on_fire_threshold*100}}}% or more votes in the last 24 hours."><i class="fa fa-fire"></i></span>
              </td>
              <td align=left>
                {{{$candidate->name}}} is on fire because the vote count has increased by {{{Candidate::$on_fire_threshold*100}}}% or more in the last 24 hours. Congratulations!
              </td>
            </tr>
          @endif
          @foreach($candidate->badges as $badge)
            <tr>
              <td>
                  <span class="badge badge-giver" title="Pledges 25% or more of cash winnings to {{{$badge->pivot->charity_name}}}."><i class="fa fa-heart"></i></span>
              </td>
              <td align=left>
                {{{$candidate->name}}} is a Charitable Giver and has pledged either 
                @if($badge->pivot->charity_percent)
                  {{{$badge->pivot->charity_percent}}}%
                @else
                  25%
                @endif 
                of cash winnings or 4 hours of service, or more, to <a href="{{{$badge->pivot->charity_url}}}">{{{$badge->pivot->charity_name}}}</a>.
              </td>
            </tr>
          @endforeach
        </table>
      @endif
    @endforeach
    @if(!$contest->is_ended && $candidate->is_owner && $contest->category->name != 'Bands')
      <div class="clearfix">
        <div class="pull-right" style="float:none!important;clear:both;margin-bottom:10px;">
          <a href="{{{$candidate->add_image_url}}}" class="btn btn-xl btn-primary"><i class="fa fa-plus"></i> Add Image</a>
        </div>
      </div>
    @endif

    @endif
    <script>
      function share()
      {
        FB.ui({
          method: 'share',
          href: {{json_encode($candidate->canonical_url($contest))}},
        });
      }
    </script>
  </div>
@stop
