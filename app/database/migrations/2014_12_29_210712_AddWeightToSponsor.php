<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeightToSponsor extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contest_sponsor', function(Blueprint $table)
		{
			$table->integer('weight')->default(99);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contest_sponsor', function(Blueprint $table)
		{
			$table->dropColumn('weight');
		});
	}

}
