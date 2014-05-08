<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRelationshipStatistics extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relationship_statistics', function(Blueprint $table) {
			$table->increments('id');

			$table->integer('vocabulary_id')->unsigned();
			$table->string('state', 20);
			$table->integer('total_count')->unsigned();

			$table->timestamp('measured_at');

			$table->index(array('vocabulary_id', 'state', 'measured_at'));
		});

		DB::unprepared('
			CREATE EVENT update_relationship_statistics
			ON SCHEDULE EVERY 1 HOUR
			ON COMPLETION NOT PRESERVE
			ENABLE
			DO
			BEGIN

				INSERT INTO relationship_statistics (vocabulary_id, state, total_count)
				SELECT 3, "any", COUNT(*) FROM relationships 
				  JOIN relationship_revisions ON relationship_revisions.id = relationships.latest_revision_id
				  JOIN concepts ON concepts.id = relationships.target_concept_id
				  WHERE concepts.vocabulary_id = 3;

				INSERT INTO relationship_statistics (vocabulary_id, state, total_count)
				SELECT 3, "exact", COUNT(*) FROM relationships 
				  JOIN relationship_revisions ON relationship_revisions.id = relationships.latest_revision_id
				  JOIN concepts ON concepts.id = relationships.target_concept_id
				  WHERE concepts.vocabulary_id = 3
				  AND relationship_revisions.state = "exact";

				INSERT INTO relationship_statistics (vocabulary_id, state, total_count)
				SELECT 3, "suggested", COUNT(*) FROM relationships 
				  JOIN relationship_revisions ON relationship_revisions.id = relationships.latest_revision_id
				  JOIN concepts ON concepts.id = relationships.target_concept_id
				  WHERE concepts.vocabulary_id = 3
				  AND relationship_revisions.state = "suggested";

			END
		');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared('DROP EVENT update_relationship_statistics');
		Schema::drop('relationship_statistics');
	}

}
