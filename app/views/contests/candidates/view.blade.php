@extends('contests.candidates.layout')

@section('contests.candidates.content')
  @if(!$candidate->image_id && !$candidate->is_owner)
    <div class="alert alert-warning">
      Please note - the image for this voting profile is under review. No voting can take place until the image has been approved.
    </div>
  @else
    @if($candidate->is_owner)
      @if($candidate->has_pending_images)
        <div class="alert alert-warning">
          @if($contest->category->name == 'Bands')
            Nobody can vote or share your profile yet because we are reviewing your image and music submission. Once we have approved your submission voting can begin.
          @else
            Nobody can vote or share your profile yet because you don't have a FEATURED image. We are still reviewing your images, so sit tight and <a href="{{{$candidate->add_image_url}}}">add more images</a> while you wait. Once we have approved a FEATURED image, voting can begin.
          @endif
        </div>
      @else
        @if(!$candidate->image_id)
          <div class="alert alert-danger">
            We reviewed all your images and none could be used as a featured image in this pick. Nobody can vote or share your profile yet because you do not have a FEATURED image. Please <a href="{{{$candidate->add_image_url}}}">add another one</a> and be sure to review the Featured Picture guidelines. 
          </div>
        @endif
      @endif
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
          @endif
          @if($contest->is_shareable)
            <a class="btn btn-lg btn-primary btn-half" onclick="share()"><i class="fa fa-facebook"></i> Share</a>
          @endif
          @if($contest->category->name == 'Bands')
            <a class="btn btn-md btn-primary btn-half" href="{{{$candidate->music_url}}}"><i class="fa fa-music"></i> Discover</a>
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
      <?php $is_featured = ($candidate->image_id == $image->id); ?>
      <div class="clearfix"
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
        <div id="candidate" class="candidate-large">
          <a href="{{{$image->url('original')}}}">
            <img src="{{{$image->url('mobile')}}}" alt="{{{$candidate->name}}}" title="Vote for {{{$candidate->name}}}"/>
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
          </div>      
        @endif
    </div>
    @if($contest->category->name == 'Bands')
      @if(isset($candidate->bio))
        <div>
          <strong>Bio:</strong> {{{$candidate->bio}}}
        </div>
        <br />
      @endif
      @if(isset($candidate->youtube_url))
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
  @if($candidate->is_owner && $contest->category->name != 'Bands')
    <div class="clearfix">
      <div class="pull-right">
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

@stop
