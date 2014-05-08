<?php

use Carbon\Carbon;

class RelationshipsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// DB::table('relations')->delete();

		$tag = Tag::create(array(
			'label' => 'test4',
			'description' => 'Import from test4.py',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));

		$rel = Relationship::create(array(
			'source_concept_id' => 1,
			'target_concept_id' => 2,
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));
		$rel->tags()->attach($tag->id);

		$rel = Relationship::create(array(
			'source_concept_id' => 1,
			'target_concept_id' => 3,
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));
		$rel->tags()->attach($tag->id);

		$rel = Relationship::create(array(
			'source_concept_id' => 1,
			'target_concept_id' => 4,
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));
		$rel->tags()->attach($tag->id);

		$rel = Relationship::create(array(
			'source_concept_id' => 5,
			'target_concept_id' => 2,
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));
		$rel->tags()->attach($tag->id);

	}

}