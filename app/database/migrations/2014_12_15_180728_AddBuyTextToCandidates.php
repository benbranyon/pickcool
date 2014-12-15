<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyTextToCandidates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('candidates', function(Blueprint $table)
		{
      $table->string('buy_text');
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('candidates', function(Blueprint $table)
		{
			$table->dropColumn('buy_text');
		});
	}

}
