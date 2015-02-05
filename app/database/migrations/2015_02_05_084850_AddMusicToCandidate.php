<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMusicToCandidate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('candidates', function(Blueprint $table)
		{
			$table->string('music_url')->nullable();
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
			$table->dropColumn('music_url');
		});
	}

}
