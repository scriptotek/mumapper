<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
//use Jenssegers\Mongodb\Schema\Blueprint;

class CreateRelationshipRevisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relationship_revisions', function(Blueprint $table) {

			$table->increments('id');
			$table->integer('parent_revision')
				->unsigned()
				->nullable()  // First revision will have parent_rev NULL
				->unique();

			$table->integer('relationship_id')->unsigned();
			$table->integer('created_by')->unsigned();
			//$table->integer('reviewed_by')->unsigned()->nullable();
			$table->string('state', 10); /* array(
				'suggested',
				'exact',
				'close',
				'broad',
				'narrow',
				'related',
				'rejected',
			));*/

			//$table->string('comment')->nullable();
			$table->timestamps();
			$table->datetime('reviewed_at')->nullable();
			$table->softDeletes();

			$table->foreign('parent_revision')
				->references('id')->on('relationship_revisions');

			$table->foreign('relationship_id')
				->references('id')->on('relationships')
				->onDelete('cascade');

			$table->foreign('created_by')
				->references('id')->on('users')
				->onDelete('cascade');

			$table->foreign('reviewed_by')
				->references('id')->on('users')
				->onDelete('cascade');

			$table->index('state');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('relationship_revisions');
	}

}
