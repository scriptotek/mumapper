<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
// use Jenssegers\Mongodb\Schema\Blueprint;

class CreateRelationshipsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relationships', function(Blueprint $table) {

			//$collection->unique(array('source_concept_id', 'target_concept_id'));

			$table->increments('id');
			$table->integer('latest_revision_id')
				->unsigned()
				->nullable()
				->unique();
			
			// "Cache" the state of the latest, so we don't have to join all the time
			// Updated using a MySQL trigger to avoid inconsistency
			$table->string('latest_revision_state', 10); /*array(
				'suggested',
				'exact',
				'close',
				'broad',
				'narrow',
				'related',
				'rejected',
			));*/

			$table->integer('source_concept_id')->unsigned();
			$table->integer('target_concept_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('source_concept_id')
				->references('id')->on('concepts')
				->onDelete('cascade');

			$table->foreign('target_concept_id')
				->references('id')->on('concepts')
				->onDelete('cascade');

			$table->unique(array(
				'source_concept_id', 'target_concept_id'
			));

			$table->index('latest_revision_state');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('relationships');
	}

}
