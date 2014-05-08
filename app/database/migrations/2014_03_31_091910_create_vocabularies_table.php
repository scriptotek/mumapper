<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
// use Jenssegers\Mongodb\Schema\Blueprint;

class CreateVocabulariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vocabularies', function(Blueprint $table) {

			//$collection->unique(array('vocabulary', 'identifier'));

			$table->increments('id');
			$table->string('uri_base');
			$table->string('label')->unique();
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
		Schema::drop('vocabularies');
	}

}
