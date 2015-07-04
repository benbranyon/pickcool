<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalloutToContest extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contests', function(Blueprint $table)
		{
			$table->longtext('callout')->nullable();
		});
    
    $callouts = [];
    $callouts[34] = <<<CALLOUT
<hr />
<div>
  <a style="color:black;" href="https://www.facebook.com/ivyleague.allure?fref=ts">Ivy League Allure Presents Colorado's Favorite Female's Grand Prize of $500</a>
  <a href="https://www.facebook.com/ivyleague.allure?fref=ts"><img style="max-width:250px;margin:0 auto;margin-top:5px;" alt="Ivy League Allure" class="img-responsive" src="/assets/img/ivy-league.jpg" /></a>
</div>
<hr />   
CALLOUT;
    $callouts[37]=<<<CALLOUT
<hr />
<div>
  <a style="color:black;" href="https://www.facebook.com/ivyleague.allure?fref=ts">Ivy League Allure Presents Texas' Favorite Female's Grand Prize of $500</a>
  <a href="https://www.facebook.com/ivyleague.allure?fref=ts"><img style="max-width:250px;margin:0 auto;margin-top:5px;" alt="Ivy League Allure" class="img-responsive" src="/assets/img/ivy-league.jpg" /></a>
</div>
<hr />   
CALLOUT;
    $callouts[35] = <<<CALLOUT
  <hr />
  <div>
    <a style="color:black;" href="https://www.facebook.com/pages/The-Decibel-Garden/249098465138620">The Decibel Garden Presents Colorado's Favorite Musicians Grand Prize of 8-hour recording session</a>
    <a href="https://www.facebook.com/pages/The-Decibel-Garden/249098465138620"><img style="max-width:250px;margin:0 auto;margin-top:5px;" alt="The Decibel Garden" class="img-responsive" src="/assets/img/decibel-garden.jpg" /></a>
  </div>
  <hr />   
CALLOUT;
    $callouts[29]=<<<CALLOUT
<hr />
<div>
  <a style="color:black;" href="http://www.janugget.com/entertainment/celebrity-showroom.html">Music, Models, and Ink Awards Ceremony. February 19.</a>
  <a href="http://www.janugget.com/entertainment/celebrity-showroom.html"><img style="max-width:150px;margin:0 auto;" alt="John Ascuaga's Nugget" class="img-responsive" src="/assets/img/nugget-color-logo.jpg" /></a>
</div>
<hr />     
CALLOUT;

    foreach($callouts as $id=>$v)
    {
      $c = Contest::find($id);
      $c->callout = $v;
      $c->save();
    }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contests', function(Blueprint $table)
		{
			$table->dropColumn('callout');
		});
	}

}
