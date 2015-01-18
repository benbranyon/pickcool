<p>The following image for <a href="{{{$image->contest->canonical_url}}}">{{{$image->contest->title}}}</a> has been returned to you and cannot be used in the pick. 
 
<p><img src="{{{$image->url('thumb')}}}"/>
  
<p>Please <a href="{{{$image->candidate->canonical_url}}}">visit your voting profile</a> and submit another image for approval.
  
<p>We appreciate your participation and invite you to submit another image. Before submitting another image, you can review the  <a href="{{{$image->contest->canonical_url}}}">pick rules</a> for specifics on the acceptable image policy.
  
@if(!$image->contest->image_id)
  <p>Important note: Your pick voting profile will not be visible or active until you submit at least one approved Featured Image.
@endif
