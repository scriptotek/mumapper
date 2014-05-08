<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddConceptDraft extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('concepts', function(Blueprint $table) {
            $table->boolean('draft');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('concepts', function(Blueprint $table) {
            $table->dropColumn('draft');
		});
	}

}
