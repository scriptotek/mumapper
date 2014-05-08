<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
// use Jenssegers\Mongodb\Schema\Blueprint;

class CreateConceptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('concepts', function(Blueprint $table) {

			//$collection->unique(array('vocabulary', 'identifier'));

			$table->increments('id');
			$table->integer('vocabulary_id')->unsigned();
			$table->string('identifier'); // Esp. for UDC, it's useful to disambiguate between the identifier and the class notation
			$table->string('notation')->nullable();
			$table->text('data');
			$table->timestamps();
			$table->unique(array('vocabulary_id', 'identifier'));

			$table->foreign('vocabulary_id')
				->references('id')->on('vocabularies')
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
		Schema::drop('concepts');
	}

}
