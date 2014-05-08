<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('commentable_id')->unsigned();
			$table->string('commentable_type');
			$table->integer('created_by')->unsigned();
			$table->text('content');
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('created_by')
				->references('id')->on('users')
				->onDelete('cascade');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comments');
	}

}
