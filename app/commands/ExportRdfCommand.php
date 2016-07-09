<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Carbon\Carbon;

class ExportRdfCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'export:rdf';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export RDF.';

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

		$filename = 'out.rdf';

		$this->info('');
		$this->info(sprintf('=========================[ %s ]==================================',
			strftime('%Y-%m-%d %H:%M:%S')
		));

		$lastmod = DB::table('relationship_revisions')->max('updated_at');
		$current = array('last_modified' => $lastmod);

		$stored = file_exists(public_path('export.meta.json'))
			? json_decode(file_get_contents(public_path('export.meta.json')), true)
			: array('last_modified' => '2014-01-01 00:00:00');


		$this->info(sprintf('  Last change in stored export: %s', $stored['last_modified']));
		$this->info(sprintf('     Last change in current DB: %s', $current['last_modified']));

		if ($stored['last_modified'] == $current['last_modified']) {
			$this->info('No need for a new export. Exiting.');
		} else {
			$this->info('Time for a new export');

			$controller = new RelationshipsController;

			list($args, $relationships, $sort) = $controller->getRelationships(false, ['reviewstate' => 'reviewed']);
			$rdfxml = $controller->rdfResponse($relationships, 'rdfxml');
			file_put_contents(public_path('export.rdf'), $rdfxml);
			file_put_contents(public_path('export.meta.json'),  json_encode($current));

			$this->info(sprintf('Wrote export.rdf and export.meta.json'));
			$this->info(sprintf('Completed at: %s',
				strftime('%Y-%m-%d %H:%M:%S')
			));

		}

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
