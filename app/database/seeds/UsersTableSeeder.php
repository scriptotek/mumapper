<?php

use Carbon\Carbon;

class UsersTableSeeder extends Seeder {

	public function run()
	{
		//$faker = Faker::create();
		User::create(array(
			'id' => 1,
			'email' => 'bot@biblionaut.net',
			'password' => Hash::make('admin'),
			'name' => 'BiblioBot',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		));
	}

}