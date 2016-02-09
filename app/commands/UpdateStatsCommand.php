<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Carbon\Carbon;

class UpdateStatsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'update:stats';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update stats.';

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

		$res = DB::select('
			select count(*) as cnt from relationships
			join relationship_revisions AS latest_revision
			  on relationships.latest_revision_id = latest_revision.id
			join concepts as source_concept
			  on relationships.source_concept_id = source_concept.id
			join concepts as target_concept
			  on relationships.target_concept_id = target_concept.id
			where latest_revision.state != "rejected"
			and latest_revision.state != "suggested"
			and latest_revision.reviewed_at is null
			and source_concept.vocabulary_id = 1
			and target_concept.vocabulary_id = 2
		');

		$ready_real_c = $res[0]->cnt;
		DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', ['ready', 'real', $ready_real_c]);

		$res = DB::select('
			select count(*) as cnt from relationships
			join relationship_revisions AS latest_revision
			  on relationships.latest_revision_id = latest_revision.id
			join concepts as source_concept
			  on relationships.source_concept_id = source_concept.id
			join concepts as target_concept
			  on relationships.target_concept_id = target_concept.id
			where latest_revision.state != "rejected"
			and latest_revision.state != "suggested"
			and latest_revision.reviewed_at is null
			and source_concept.vocabulary_id = 3
			and target_concept.vocabulary_id = 2
		');

		$ready_hume_c = $res[0]->cnt;
		DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', ['ready', 'hume', $ready_hume_c]);

		$res = DB::select('
			select count(*) as cnt from relationships
			join relationship_revisions AS latest_revision
			  on relationships.latest_revision_id = latest_revision.id
			join concepts as source_concept
			  on relationships.source_concept_id = source_concept.id
			join concepts as target_concept
			  on relationships.target_concept_id = target_concept.id
			where latest_revision.state != "rejected"
			and latest_revision.reviewed_at is not null
			and source_concept.vocabulary_id = 1
			and target_concept.vocabulary_id = 2
		');

		$reviewed_real_c = $res[0]->cnt;
		DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', ['reviewed', 'real', $reviewed_real_c]);

		$res = DB::select('
			select count(*) as cnt from relationships
			join relationship_revisions AS latest_revision
			  on relationships.latest_revision_id = latest_revision.id
			join concepts as source_concept
			  on relationships.source_concept_id = source_concept.id
			join concepts as target_concept
			  on relationships.target_concept_id = target_concept.id
			where latest_revision.state != "rejected"
			and latest_revision.reviewed_at is not null
			and source_concept.vocabulary_id = 3
			and target_concept.vocabulary_id = 2
		');

		$reviewed_hume_c = $res[0]->cnt;
		DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', ['reviewed', 'hume', $reviewed_hume_c]);

		$res = DB::select('
			select count(*) as cnt from relationships
			join relationship_revisions AS latest_revision
			  on relationships.latest_revision_id = latest_revision.id
			join concepts as source_concept
			  on relationships.source_concept_id = source_concept.id
			join concepts as target_concept
			  on relationships.target_concept_id = target_concept.id
			where latest_revision.state = "rejected"
			and source_concept.vocabulary_id = 1
			and target_concept.vocabulary_id = 2
		');

		$rejected_real_c = $res[0]->cnt;
		DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', ['rejected', 'real', $rejected_real_c]);

		$res = DB::select('
			select count(*) as cnt from relationships
			join relationship_revisions AS latest_revision
			  on relationships.latest_revision_id = latest_revision.id
			join concepts as source_concept
			  on relationships.source_concept_id = source_concept.id
			join concepts as target_concept
			  on relationships.target_concept_id = target_concept.id
			where latest_revision.state = "rejected"
			and source_concept.vocabulary_id = 3
			and target_concept.vocabulary_id = 2
		');

		$rejected_hume_c = $res[0]->cnt;
		DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', ['rejected', 'hume', $rejected_hume_c]);

		$this->info(sprintf('REAL: %s ready, %s reviewed, %s rejected  -  HUME: %s ready, %s reviewed, %s rejected', $ready_real_c, $reviewed_real_c, $rejected_real_c, $ready_hume_c, $reviewed_hume_c, $rejected_hume_c));


		// $opts = [
		// 	'filename' => 'µmapper stats',
		// 	'fileopt' => 'overwrite',
		// 	'style' => [
		// 		'type' => 'line',
		// 	],
		// 	'world_readable' => 'true',
		// ];

		// $client = new GuzzleHttp\Client();
		// $res = $client->request('POST', 'https://plot.ly/clientresp', [
		// 	'form_params' => [
		// 		'un' => 'danmichaelo',
		// 		'key' => Config::get('app.plotly_key'),
		// 		'origin' => 'plot',
		// 		'platform' => 'php',
		// 	    'args' => json_encode($data),
		// 	    'kwargs' => json_encode($opts),
		// 	],
		// ]);

		// print $res->getBody() . "\n";

		$this->info(sprintf('=========================[ %s ]==================================',
			strftime('%Y-%m-%d %H:%M:%S')
		));

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
