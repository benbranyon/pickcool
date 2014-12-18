<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWriteinToContest extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contests', function(Blueprint $table)
		{
			$table->boolean('writein_enabled')->default(0);
		});
		Schema::table('candidates', function(Blueprint $table)
		{
			$table->string('fb_id')->nullable();
		});

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
			$table->dropColumn('writein_enabled');
		});
		Schema::table('candidates', function(Blueprint $table)
		{
			$table->dropColumn('fb_id');
		});
	}

}
