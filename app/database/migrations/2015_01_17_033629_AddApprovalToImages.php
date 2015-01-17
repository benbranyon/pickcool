<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovalToImages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    DB::update("drop table if exists messages;");
    DB::update("alter table candidates modify image_id integer(11);");
    
		Schema::table('images', function(Blueprint $table)
		{
			$table->timestamp('screened_at')->nullable();
      $table->string('status')->nullable();
      $table->integer('candidate_id')->nullable();
		});
  
    DB::update("update images set candidate_id = (select cn.id from candidates cn join contests c on c.id = cn.contest_id where c.writein_enabled = 1 and cn.image_id = images.id and c.id=29)");

		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('user_id');
      $table->string('subject');
      $table->longtext('body');
      $table->timestamp('read_at')->nullable();
      $table->timestamps();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('images', function(Blueprint $table)
		{
			$table->dropColumn('screened_at');
      $table->dropColumn('status');
      $table->dropColumn('candidate_id');
		});
    
		Schema::drop('messages');
    
	}

}
