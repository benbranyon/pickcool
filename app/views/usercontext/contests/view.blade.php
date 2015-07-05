@if($contest->has_joined)
  $('.user-in-pick').removeClass('hidden');
@endif
@if($contest->is_editable)
  $('.edit-contest').removeClass('hidden');
@endif
