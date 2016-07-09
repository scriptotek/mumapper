<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Carbon\Carbon;

class ExportJsonCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'export:json';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export mappings as JSON.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{

		// The query log is kept in memory, so we should disable it for long-running
		// tasks to prevent memory usage from increasing linearly over time
		DB::connection()->disableQueryLog();

		$this->info('');
		$this->info(sprintf('=========================[ %s ]==================================',
			strftime('%Y-%m-%d %H:%M:%S')
		));

		$lastmod = DB::table('relationship_revisions')->max('updated_at');
		$current = array('last_modified' => $lastmod);

		// $stored = file_exists(public_path('export.meta.json'))
		// 	? json_decode(file_get_contents(public_path('export.meta.json')), true)
		// 	: array('last_modified' => '2014-01-01 00:00:00');


		// $this->info(sprintf('  Last change in stored export: %s', $stored['last_modified']));
		// $this->info(sprintf('     Last change in current DB: %s', $current['last_modified']));

		// if ($stored['last_modified'] == $current['last_modified']) {
		// 	$this->info('No need for a new export. Exiting.');
		// } else {
		// 	$this->info('Time for a new export');

		// --------------------------------------------------------------

		$controller = new RelationshipsController;

		list($args, $relationships, $sort) = $controller->getRelationships(false, [
			'reviewstate' => 'reviewed',
			'sourceVocabularies' => [1],
			'targetVocabularies' => [2],
		]);
		$out = $controller->jsonResponse(
			$relationships,
			'http://data.ub.uio.no/realfagstermer/',
			'http://dewey.info/scheme/edition/e23/',
			false,
			true
		);
		file_put_contents('compress.zlib://' . public_path('export/latest/concordance_real_wdno.json.gz'), $out);
		$this->info(sprintf('Wrote %d mappings to https://lambda.biblionaut.net/export/latest/concordance_real_wdno.json.gz', count($relationships)));

		// --------------------------------------------------------------

		$controller = new RelationshipsController;

		list($args, $relationships, $sort) = $controller->getRelationships(false, [
			'reviewstate' => 'reviewed',
			'sourceVocabularies' => [3],
			'targetVocabularies' => [2],
		]);
		$out = $controller->jsonResponse(
			$relationships,
			'http://data.ub.uio.no/humord/',
			'http://dewey.info/scheme/edition/e23/',
			false,
			true
		);
		file_put_contents('compress.zlib://' . public_path('export/latest/concordance_hume_wdno.json.gz'), $out);
		$this->info(sprintf('Wrote %d mappings to https://lambda.biblionaut.net/export/latest/concordance_hume_wdno.json.gz', count($relationships)));


		// --------------------------------------------------------------

		$controller = new RelationshipsController;

		list($args, $relationships, $sort) = $controller->getRelationships(false, [
			'reviewstate' => 'reviewed',
			'sourceVocabularies' => [1],
			'targetVocabularies' => [3],
		]);

		$out = $controller->jsonResponse(
			$relationships,
			'http://data.ub.uio.no/realfagstermer/',
			'http://data.ub.uio.no/humord/',
			true,
			true
		);
		file_put_contents('compress.zlib://' . public_path('export/latest/concordance_real_hume.json.gz'), $out);
		$this->info(sprintf('Wrote %d mappings to https://lambda.biblionaut.net/export/latest/concordance_real_hume.json.gz', count($relationships)));

		// --------------------------------------------------------------

		$this->info(sprintf('Completed at: %s',
			strftime('%Y-%m-%d %H:%M:%S')
		));

		// }

		// $this->info(sprintf('@ Processed %d records', $recordsProcessed));
		// $this->info(sprintf('@ %s: Completing OAI harvest',
		// 	strftime('%Y-%m-%d %H:%M:%S')
		// ));
		// $this->info('------------------------------------------------------------');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('filename', InputArgument::REQUIRED, 'The RDF/SKOS filename'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(

		);
	}

}
