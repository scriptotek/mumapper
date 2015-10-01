<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
// use Jenssegers\Mongodb\Schema\Blueprint;

class CreateLabelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('labels', function(Blueprint $table) {

			//$collection->unique(array('vocabulary', 'identifier'));

			$table->increments('id');
			$table->integer('concept_id')->unsigned();
			$table->enum('class', array('prefLabel', 'altLabel', 'hiddenLabel'));
			$table->string('lang');
			$table->string('value');
			$table->unique(array('concept_id', 'class', 'lang', 'value'));
			$table->timestamps();

			$table->foreign('concept_id')
				->references('id')->on('concepts')
				->onDelete('cascade');

		});

		DB::unprepared('
			ALTER TABLE `labels`
			DEFAULT CHARACTER SET utf8
			COLLATE utf8_bin
		');

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('labels');
	}

}
