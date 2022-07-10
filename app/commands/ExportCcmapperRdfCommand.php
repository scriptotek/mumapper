<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Carbon\Carbon;

EasyRdf\RdfNamespace::set('ubo', 'http://data.ub.uio.no/onto#');
EasyRdf\RdfNamespace::set('hume', 'http://data.ub.uio.no/humord/');
EasyRdf\RdfNamespace::set('real', 'http://data.ub.uio.no/realfagstermer/');


class ExportCcmapperRdfCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'export:ccmapper-rdf';

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
		$this->info('');
		$this->info(sprintf('=========[ %s : Export CCMapper mappings as RDF ]===========',
			strftime('%Y-%m-%d %H:%M:%S')
		));

		// The query log is kept in memory, so we should disable it for long-running
		// tasks to prevent memory usage from increasing linearly over time
		$cc_db = DB::connection('mysql_ccmapper');
		$cc_db->disableQueryLog();

		EasyRdf\RdfNamespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');

		$graph = new EasyRdf\Graph();

		$skosMap = [
			'EXACT_EQUIVALENCE' => 'skos:exactMatch', //  But due to the way we define =EQ, this is not really right... :/
			'CLOSE_EQUIVALENCE' => 'skos:closeMatch',
			'BROADER_MAPPING' => 'skos:broadMatch',
			'RELATED_MAPPING' => 'skos:relatedMatch',
		];

		$vocabMap = [];
		$vocabMap['ddc'] = Vocabulary::where('label', 'WDNO')->first();
		$target_vocab_id = $vocabMap['ddc']->id;

		$res = $cc_db->select("
			select
				CONCEPT.CONCEPT_ID as source_concept,
				MAPPING.ID_TARGET as target_concept,
				MAPPING.TYPE as mapping_type
			from
				MAPPING
			left join
				MAPPING_SOURCE on MAPPING.ID_SOURCE = MAPPING_SOURCE.ID
			left join
				CONCEPT on MAPPING.ID_SOURCE = CONCEPT.CONCEPT_ID
			where
				MAPPING_SOURCE.MAPPING_STATE IN ('mappingCompleted', 'review', 'accepted')
			and
				CONCEPT.CONCEPT_ID is not NULL
			and
				MAPPING.TYPE IN ('EXACT_EQUIVALENCE', 'CLOSE_EQUIVALENCE', 'BROADER_MAPPING', 'RELATED_MAPPING')
		");

		foreach ($res as $rel) {
			$sv = substr($rel->source_concept, 0, 4);
			$si = substr($rel->source_concept, 4);
			$tv = substr($rel->target_concept, 0, 3);
			$ti = substr($rel->target_concept, 4);
			$ti = str_replace('T', '', $ti);

			if (!isset($vocabMap[$sv])) {
				$vocabMap[$sv] = Vocabulary::where('label', $sv)->first();
			}

			$source_vocab = $vocabMap[$sv];
			$target_vocab = $vocabMap[$tv];

			$source_uri = str_replace('{identifier}', $si, $source_vocab->uri_base);
			$target_uri = str_replace('{identifier}', $ti, $target_vocab->uri_base);
			$predicate = $skosMap[$rel->mapping_type];

			$source = $graph->resource($source_uri, 'skos:Concept');
			$target = $graph->resource($target_uri);

			$source->add($predicate, $target);
        }

		$res = $cc_db->select("
            select
                MAPPING_SOURCE.ID as source_concept,
                MAPPING_SOURCE.MAPPING_STATE as source_state
			from
				MAPPING_SOURCE
		");
        foreach ($res as $rel) {
			$sv = substr($rel->source_concept, 0, 4);
			$si = substr($rel->source_concept, 4);

			if (!in_array($sv, ['REAL', 'HUME'])) {
				continue;
			}

			if (!isset($vocabMap[$sv])) {
				$vocabMap[$sv] = Vocabulary::where('label', $sv)->first();
			}

			$source_vocab = $vocabMap[$sv];
			$source_uri = str_replace('{identifier}', $si, $source_vocab->uri_base);
			$source = $graph->resource($source_uri, 'skos:Concept');
			$predicate = EasyRdf\RdfNamespace::expand('ubo:ccmapperState');
			$value = $rel->source_state;

			$source->add($predicate, $value);
        }

		$res = $cc_db->select("
            select
				MAPPING_CANDIDATE.ID_SOURCE as source_concept,
				count(*) as candidates
			from
				MAPPING_CANDIDATE
			group by
				MAPPING_CANDIDATE.ID_SOURCE
		");

		foreach ($res as $rel) {
			$sv = substr($rel->source_concept, 0, 4);
			$si = substr($rel->source_concept, 4);

			if (!in_array($sv, ['REAL', 'HUME'])) {
				continue;
			}

			if (!isset($vocabMap[$sv])) {
				$vocabMap[$sv] = Vocabulary::where('label', $sv)->first();
			}

			$source_vocab = $vocabMap[$sv];
			$source_uri = str_replace('{identifier}', $si, $source_vocab->uri_base);
			$source = $graph->resource($source_uri, 'skos:Concept');
			$predicate = EasyRdf\RdfNamespace::expand('ubo:ccmapperCandidates');
			$value = $rel->candidates;

			$source->add($predicate, $value);
		}
		# Finally output the graph
		file_put_contents(
			public_path('export/ccmapper_mappings.ttl'),
			$graph->serialise('turtle')
		);

		$this->line('Updated https://lambda.biblionaut.net/export/ccmapper_mappings.ttl');

		// $this->info('');
		// $this->info(sprintf('=========================[ %s ]==================================',
		// 	strftime('%Y-%m-%d %H:%M:%S')
		// ));

		// $lastmod = DB::table('relationship_revisions')->max('updated_at');
		// $current = array('last_modified' => $lastmod);

		// $stored = file_exists(public_path('export.meta.json'))
		// 	? json_decode(file_get_contents(public_path('export.meta.json')), true)
		// 	: array('last_modified' => '2014-01-01 00:00:00');


		// $this->info(sprintf('  Last change in stored export: %s', $stored['last_modified']));
		// $this->info(sprintf('     Last change in current DB: %s', $current['last_modified']));

		// if ($stored['last_modified'] == $current['last_modified']) {
		// 	$this->info('No need for a new export. Exiting.');
		// } else {
		// 	$this->info('Time for a new export');

		// 	$controller = new RelationshipsController;

		// 	list($args, $relationships, $sort) = $controller->getRelationships(false, ['reviewstate' => 'reviewed']);
		// 	$rdfxml = $controller->rdfResponse($relationships, 'rdfxml');
		// 	file_put_contents(public_path('export.rdf'), $rdfxml);
		// 	file_put_contents(public_path('export.meta.json'),  json_encode($current));

		// 	$this->info(sprintf('Wrote export.rdf and export.meta.json'));
		// 	$this->info(sprintf('Completed at: %s',
		// 		strftime('%Y-%m-%d %H:%M:%S')
		// 	));

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
