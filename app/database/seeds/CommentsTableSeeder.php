<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class CommentsTableSeeder extends Seeder {

	public function run()
	{
		// $faker = Faker::create();

		// foreach(range(1, 10) as $index)
		// {
		Comment::create([
			'created_by' => 1,
			'commentable_id' => 1,
			'commentable_type' => 'RelationshipRevision',
			'content' => 'Imported from test3.py run',
		]);

		// }
	}

}