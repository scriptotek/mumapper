<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		DB::statement('SET foreign_key_checks = 0');

		$this->call('UsersTableSeeder');
		$this->call('VocabulariesTableSeeder');
/*		
		$this->call('ConceptsTableSeeder');
		$this->call('LabelsTableSeeder');
		$this->call('RelationshipsTableSeeder');
		$this->call('RelationshipRevisionsTableSeeder');
		$this->call('CommentsTableSeeder');
 */
		DB::statement('SET foreign_key_checks = 1');
	}

}
