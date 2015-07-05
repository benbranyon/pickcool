var recs = {{json_encode($recs)}}
for(var i in recs)
{
  var rec = recs[i];
  $('.candidate-'+rec.current_user_candidate_id).addClass('selected');
  if(rec.is_editable)
  {
    $('.contest-'+rec.id+' .is-editable').removeClass('hidden');
  }
}
