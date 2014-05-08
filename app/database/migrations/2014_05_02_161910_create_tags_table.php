<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tags', function(Blueprint $table) {
			$table->increments('id');
			$table->string('label');
			$table->text('description')->nullable();
			$table->timestamps();
		});

		Schema::create('relationship_tag', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('relationship_id')->unsigned();
			$table->integer('tag_id')->unsigned();

			$table->foreign('relationship_id')
				->references('id')->on('relationships')
				->onDelete('cascade');

			$table->foreign('tag_id')
				->references('id')->on('tags')
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
		Schema::drop('relationship_tag');
		Schema::drop('tags');
	}

}
