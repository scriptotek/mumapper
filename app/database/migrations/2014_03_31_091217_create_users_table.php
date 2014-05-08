<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
// use Jenssegers\Mongodb\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('name')->nullable();
			$table->string('password')->nullable();
			$table->string('remember_token')->nullable();
			$table->timestamps();
		});

		// Prøve Aria?
		// DB::unprepared('ALTER TABLE `tablename` ENGINE=`Aria` TRANSACTIONAL=1;');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
