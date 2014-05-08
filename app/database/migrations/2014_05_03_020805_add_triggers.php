<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTriggers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// This version of MariaDB doesn't yet support 'multiple triggers  
   		// with the same action time and event for one table, 
   		// so we have to combine queries into one trigger 
		DB::unprepared('
			CREATE TRIGGER `on_relationships_revisions_insert`
			AFTER INSERT ON `relationship_revisions`
			FOR EACH ROW
			BEGIN

				UPDATE `relationships`
					SET latest_revision_id = NEW.id
					, latest_revision_state = NEW.state
				WHERE id = NEW.relationship_id;

				INSERT INTO `activity` (activity_id, activity_model, created_by, created_at, updated_at)
				VALUES (NEW.id, "RelationshipRevision", NEW.created_by, NOW(), NOW());
			END
		');

		DB::unprepared('
			CREATE TRIGGER `on_comments_insert`
			AFTER INSERT ON `comments`
			FOR EACH ROW
			BEGIN
				INSERT INTO `activity` (activity_id, activity_model, created_by, created_at, updated_at)
				VALUES (NEW.id, "Comment", NEW.created_by, NOW(), NOW());
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
		DB::unprepared('DROP TRIGGER `on_relationships_revisions_insert`');
		DB::unprepared('DROP TRIGGER `on_comments_insert`');
	}

}
