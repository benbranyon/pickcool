<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPercentCharity extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('badge_candidate', function(Blueprint $table)
		{
			$table->string('charity_percent')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('badge_candidate', function(Blueprint $table)
		{
			$table->dropColumn('charity_percent');
		});
	}

}
