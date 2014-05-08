<?php

use Carbon\Carbon;

class LabelsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		Label::create([
			'concept_id' => 1,
			'lang' => 'nb',
			'class' => 'prefLabel',
			'value' => 'Fysikk'
		]);

		Label::create([
			'concept_id' => 1,
			'lang' => 'en',
			'class' => 'prefLabel',
			'value' => 'Physics'
		]);

		Label::create([
			'concept_id' => 2,
			'lang' => 'nb',
			'class' => 'prefLabel',
			'value' => 'Fysikk'
		]);

		Label::create([
			'concept_id' => 3,
			'lang' => 'en',
			'class' => 'prefLabel',
			'value' => 'Physics'
		]);

		Label::create([
			'concept_id' => 4,
			'lang' => 'en',
			'class' => 'prefLabel',
			'value' => 'Physics'
		]);

		Label::create([
			'concept_id' => 5,
			'lang' => 'nb',
			'class' => 'prefLabel',
			'value' => 'Fysikk : historie'
		]);

	}

}