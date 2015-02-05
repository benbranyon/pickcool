<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function(Blueprint $table)
		{
			$table->increments('id');
      		$table->string('name');
			$table->timestamps();
		});

		Schema::table('contests', function(Blueprint $table)
		{
			$table->string('category_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories');

		Schema::table('contests', function(Blueprint $table)
		{
			$table->dropColumn('category_id');
		});
	}

}
