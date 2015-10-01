<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpAndUserAgentToVote extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->string('ip_address')->nullable();
      $table->string('user_agent')->nullable();
      $table->longtext('audit')->nullable();
      $table->index('ip_address')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->dropColumn('ip_address');
			$table->dropColumn('audit');
			$table->dropColumn('user_agent');
		});
	}

}
