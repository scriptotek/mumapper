<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRelationshipKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('relationships', function(Blueprint $table) {

			$table->foreign('latest_revision_id')
				->references('id')->on('relationship_revisions')
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
		Schema::table('relationships', function(Blueprint $table) {
			$table->dropForeign('relationships_latest_revision_id_foreign');
		});
	}

}
