<?php

use Carbon\Carbon;

class VocabulariesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// DB::table('relations')->delete();

		Vocabulary::create([
			'label' => 'RT',
			'uri_base' => 'http://folk.uio.no/knuthe/emne/data/xml/#{identifier}',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Vocabulary::create([
			'label' => 'TEK',
			'uri_base' => 'http://ntnu.no/ub/data/tekord#{identifier}',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Vocabulary::create([
			'label' => 'DDK23',
			'uri_base' => 'http://dewey.info/class/{identifier}/e23/',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);

		Vocabulary::create([
			'label' => 'UDK',
			'uri_base' => 'http://udcdata.info/{identifier}',
			'created_at' => Carbon::Now(),
			'updated_at' => Carbon::Now(),
		]);
		// <skos:Concept rdf:about="http://udcdata.info/027739">
		// <skos:inScheme rdf:resource="http://udcdata.info/udc-schema"/>
		// <skos:broader rdf:resource="http://udcdata.info/025403"/>
		// <skos:notation rdf:datatype="http://udcdata.info/UDCnotation">53</skos:notation>



	}

}