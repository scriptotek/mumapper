<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVocabularyBsindex extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vocabularies', function(Blueprint $table) {
            $table->string('bs_cql_query')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vocabularies', function(Blueprint $table) {
            $table->dropColumn('bs_cql_query');
		});
	}

}
