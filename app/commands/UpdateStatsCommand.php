<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Carbon\Carbon;
use RunningStat\RunningStat;



/**
 * Calculate mean (simple arithmetic average).
 *
 * @param array $values
 * @return string Mean
 */
function mean(array $values) {
	$sum = array_sum($values);
	return $sum / count($values);
}

/**
 * Calculate median.
 *
 * @param array $values
 * @return string Median value
 */
function median(array $values) {
	sort($values, SORT_NUMERIC);
	$n = count($values);

	// exact median
	if (isset($values[$n/2])) {
		return $values[$n/2];
	}

	// average of two middle values
	$m1 = ($n-1)/2;
	$m2 = ($n+1)/2;
	if (isset($values[$m1]) && isset($values[$m2])) {
		return ($values[$m1] + $values[$m2]) / 2;
	}

	// best guess
	$mrnd = (int) round($n/2, 0);
	if (isset($values[$mrnd])) {
		return $values[$mrnd];
	}
	return null;
}



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

		// 1.1 REAL-WDNO: number of mappings where state not in ("rejected", "suggested") and reviewed is null
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

		// 1.2 HUME-WDNO: number of mappings where state not in ("rejected", "suggested") and reviewed is null
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

		// 2.1 REAL-WDNO: number of reviewed_mappings
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

		// 2.2 HUME-WDNO: number of reviewed mappings
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

		// 3.1 REAL-WDNO: number of rejected mappings
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

		// 3.2 HUME-WDNO: number of rejected mappings
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


		// 4 Number of concepts with mappings (by bot/non-bot)
		$vocabs = [
			1 => 'real',
			3 => 'hume',
		];
		$metrics = [
			'concepts_having_bot_mappings' => [
				'{Q1}' => 'AND first_rev.created_by = 1',
			],
			'concepts_having_non_bot_mappings' => [
				'{Q1}' => 'AND first_rev.created_by != 1',
			],
			'concepts_having_mappings' => [
				'{Q1}' => '',
			],
		];
		$baseQuery = '
			SELECT
			  concepts.identifier as ident,
			  COUNT(rel.id) as relc

			FROM concepts

			JOIN relationships AS rel
			  ON rel.source_concept_id = concepts.id

			JOIN concepts AS target_concept
			  ON rel.target_concept_id = target_concept.id
              AND target_concept.vocabulary_id = 2

			JOIN relationship_revisions as first_rev
			  ON first_rev.relationship_id = rel.id
			  AND first_rev.parent_revision IS NULL
			  {Q1}

			JOIN relationship_revisions as last_rev
			  ON rel.latest_revision_id = last_rev.id
			  AND last_rev.state NOT IN ("suggested", "rejected")
			  AND last_rev.reviewed_at IS NOT NULL

			WHERE concepts.vocabulary_id = {vocab_id}
			GROUP BY concepts.identifier
		';
		foreach ($metrics as $metric_name => $metric) {
			foreach ([1, 3] as $vocab_id) {
				$query = str_replace(array_keys($metric), array_values($metric), $baseQuery);
				$query = str_replace('{vocab_id}', $vocab_id, $query);

				$res = DB::select($query);
				$nconcepts = 0;
				$nmappings = 0;
				foreach ($res as $row) {
					$nconcepts++;
					$nmappings += $row->relc;
				}

				DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', [
					$metric_name,
					$vocabs[$vocab_id],
					$nconcepts,
				]);

				DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', [
					$metric_name . '_mappings',
					$vocabs[$vocab_id],
					$nmappings,
				]);
			}
		}



		// 5 Number of mappings by concept type
		$vocabs = [
			1 => 'real',
			3 => 'hume',
		];
		$baseQuery = '
			SELECT
			  last_rev.state as state,
			  COUNT(*) as cnt

			FROM relationships AS rel

			JOIN concepts AS source_concept
			  ON rel.source_concept_id = source_concept.id

			JOIN concepts AS target_concept
			  ON rel.target_concept_id = target_concept.id
              AND target_concept.vocabulary_id = 2

			JOIN relationship_revisions as last_rev
			  ON rel.latest_revision_id = last_rev.id
			  AND last_rev.reviewed_at IS NOT NULL

			WHERE source_concept.vocabulary_id = {vocab_id}
			GROUP BY last_rev.state
		';
		$baseCat = 'reviewed_mappings_{}';
		foreach ([1, 3] as $vocab_id) {
			$query = str_replace('{vocab_id}', $vocab_id, $baseQuery);
			$res = DB::select($query);
			foreach ($res as $row) {
				$metric_name = str_replace('{}', $row->state, $baseCat);
				$cnt = $row->cnt;

				DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', [
					$metric_name,
					$vocabs[$vocab_id],
					$cnt,
				]);
			}
		}

		// 6 Number of verified mappings created by bot
		$vocabs = [
			1 => 'real',
			3 => 'hume',
		];
		$baseQuery = '
			SELECT
			  COUNT(*) as cnt

			FROM relationships AS rel

			JOIN concepts AS source_concept
			  ON rel.source_concept_id = source_concept.id

			JOIN concepts AS target_concept
			  ON rel.target_concept_id = target_concept.id
              AND target_concept.vocabulary_id = 2

			JOIN relationship_revisions as first_rev
			  ON first_rev.relationship_id = rel.id
			  AND first_rev.parent_revision IS NULL

			JOIN relationship_revisions as last_rev
			  ON rel.latest_revision_id = last_rev.id
			  AND last_rev.state NOT IN ("suggested", "rejected")
			  AND last_rev.reviewed_at IS NOT NULL

			WHERE source_concept.vocabulary_id = {vocab_id}
			AND first_rev.created_by = 1
		';
		foreach ([1, 3] as $vocab_id) {
			$query = str_replace('{vocab_id}', $vocab_id, $baseQuery);
			$res = DB::select($query);
			$cnt = $res[0]->cnt;

			DB::insert('insert into stats (category, vocabulary, value) values (?, ?, ?)', [
				'verified_mappings_by_bot',
				$vocabs[$vocab_id],
				$cnt,
			]);
		}

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
