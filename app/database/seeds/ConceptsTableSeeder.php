<?php

use Carbon\Carbon;

class ConceptsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// DB::table('relations')->delete();

		Concept::create([
			'vocabulary_id' => 1,
			'identifier' => 'REAL03411',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Concept::create([
			'vocabulary_id' => 2,
			'identifier' => 'NTUB03815',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Concept::create([
			'vocabulary_id' => 3,
			'identifier' => '530',
			'notation' => '530',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Concept::create([
			'vocabulary_id' => 4,
			'identifier' => '027739',
			'notation' => '53',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Concept::create([
			'vocabulary_id' => 1,
			'identifier' => 'REAL03410',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);
	}

}