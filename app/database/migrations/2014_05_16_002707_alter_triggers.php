<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTriggers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared('DROP TRIGGER `on_relationships_revisions_insert`');
		DB::unprepared('
			CREATE TRIGGER `on_relationships_revisions_insert`
			AFTER INSERT ON `relationship_revisions`
			FOR EACH ROW
			BEGIN

				UPDATE `relationships`
					SET latest_revision_id = NEW.id
					, latest_revision_state = NEW.state
					, updated_at = NEW.created_at
				WHERE id = NEW.relationship_id;

				INSERT INTO `activity` (activity_id, activity_model, created_by, created_at, updated_at)
				VALUES (NEW.id, "RelationshipRevision", NEW.created_by, NEW.created_at, NEW.updated_at);
			END
		');

		DB::unprepared('DROP TRIGGER `on_comments_insert`');
		DB::unprepared('
			CREATE TRIGGER `on_comments_insert`
			AFTER INSERT ON `comments`
			FOR EACH ROW
			BEGIN
				INSERT INTO `activity` (activity_id, activity_model, created_by, created_at, updated_at)
				VALUES (NEW.id, "Comment", NEW.created_by, NEW.created_at, NEW.updated_at);
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
		DB::unprepared('
			CREATE TRIGGER `on_relationships_revisions_insert`
			AFTER INSERT ON `relationship_revisions`
			FOR EACH ROW
			BEGIN

				UPDATE `relationships`
					SET latest_revision_id = NEW.id
					, latest_revision_state = NEW.state
					, updated_at = NOW()
				WHERE id = NEW.relationship_id;

				INSERT INTO `activity` (activity_id, activity_model, created_by, created_at, updated_at)
				VALUES (NEW.id, "RelationshipRevision", NEW.created_by, NOW(), NOW());
			END
		');

		DB::unprepared('DROP TRIGGER `on_comments_insert`');
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

}
