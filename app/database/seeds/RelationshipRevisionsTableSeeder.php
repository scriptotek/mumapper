<?php

use Carbon\Carbon;

class RelationshipRevisionsTableSeeder extends Seeder {

	public function run()
	{
		RelationshipRevision::create(array(
			'relationship_id' => 1,
			'created_by' => 1,
			'state' => 'suggested',
			//'comment' => 'Imported by test3.py',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));

		RelationshipRevision::create(array(
			'relationship_id' => 2,
			'created_by' => 1,
			'state' => 'suggested',
			//'comment' => 'Imported by test3.py',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));

		RelationshipRevision::create(array(
			'relationship_id' => 3,
			'created_by' => 1,
			'state' => 'suggested',
			//'comment' => 'Imported by test3.py',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));

		RelationshipRevision::create(array(
			'relationship_id' => 4,
			'created_by' => 1,
			'state' => 'suggested',
			//'comment' => 'Imported by test3.py',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));

	}

}