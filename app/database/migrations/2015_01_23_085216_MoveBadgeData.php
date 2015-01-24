<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveBadgeData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('candidate_badge', function($table)
		{
			$table->string('charity_name')->nullable();
			$table->string('charity_url')->nullable();
		});

		DB::update('insert into candidate_badge (contest_id, candidate_id, charity_name, charity_url) select contest_id, id, charity_name, charity_url from candidates where charity_name is not null');
		DB::update("insert into badges (name, vote_weight) values ('charity', 25)");
		DB::update("update candidate_badge set badge_id = (select id from badges where name = 'charity')");

		Schema::table('candidates', function($table)
		{
		    $table->dropColumn('charity_name');
		    $table->dropColumn('charity_url');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
