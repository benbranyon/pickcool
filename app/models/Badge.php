<?php
  
class Badge extends Eloquent
{

	public function candidates()
	{
		return $this->belongsToMany('Candidate');
	}

}
