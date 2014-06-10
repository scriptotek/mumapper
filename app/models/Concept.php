<?php

// use Jenssegers\Mongodb\Model as Eloquent;

class Concept extends BaseModel implements CommentableInterface {

	/**
	 * Validation rules
	 * 
	 * @var Array
	 */
	protected static $rules = array(
		'vocabulary_id' => 'required|integer|exists:vocabularies,id',
		'identifier'    => 'required|unique_with:concepts,vocabulary_id',
	);

	// public $data;
	// public $created_at;
	// public $identifier;
	// public $vocabulary;

	/**
	 * Accessor for the data field
	 */
	public function getDataAttribute($value)
	{
		return json_decode($value, true);
	}

	/**
	 * Mutator for the data field
	 */
	public function setDataAttribute($value)
	{
		$this->attributes['data'] = json_encode($value);
	}

	// public function sourceConcepts()
	// {
	//     return $this->belongsToMany('Concept', 'relations', 'target_concept_id', 'source_concept_id');
	//  //       ->withPivot('id');
	// }

	// public function targetConcepts()
	// {
	//     return $this->belongsToMany('Concept', 'relations', 'source_concept_id', 'target_concept_id');
	// }

	public function comments()
	{
		return $this->morphMany('Comment', 'commentable');
	}

	public function sourceRelationships()
	{
		return $this->hasMany('Relationship', 'source_concept_id');
	}

	public function targetRelationships()
	{
		return $this->hasMany('Relationship', 'target_concept_id');
	}

	public function labels()
	{
		return $this->hasMany('Label');
	}

	public function vocabulary()
	{
		return $this->belongsTo('Vocabulary');
	}

	public function uri()
	{
		return str_replace('{identifier}', $this->identifier, $this->vocabulary->uri_base);
	}

	/**
	 * Return a prefLabel in the preferred language $lang 
	 * or one of the fallback languages in $fallbackChain
	 */
	public function prefLabel($lang='nb', $fallbackChain=array('en'))
	{
		$labels = [];
		// TODO: make more robust!
		foreach ($this->labels as $lab) {
			if ($lab->class == 'prefLabel') {
				$labels[$lab->lang] = $lab->value;
			}
		}
		if (isset($labels[$lang])) return $labels[$lang];
		foreach ($fallbackChain as $lang) {
			if (isset($labels[$lang])) return $labels[$lang];
		}
		return ' (ERROR: no prefLabel found) ';
	}

	public function rdfRepresentation($serialization = 'turtle') {
		EasyRdf_Namespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');
		//EasyRdf_TypeMapper::set('skos:Concept', 'Concept');

		$graph = new EasyRdf_Graph();

		$source = $graph->resource($this->uri(), 'skos:Concept');

		foreach ($this->labels as $lab) {
			$source->addLiteral('skos:' . $lab->class, $lab->value, $lab->lang);
		}

		foreach ($this->sourceRelationships as $rel) {
			$predicate = $rel->stateAsSkos();
			if ($predicate) {
				$target = $graph->resource($rel->targetConcept->uri());
				$source->add($predicate, $target);
			}
		}

		return $graph->serialise($serialization);
	}

	/**
	 * Return a HTML representation 
	 * TODO: Choose format: JSON, RDF, ...
	 */
	public function representation($prefix = false, $link = true)
	{
		$lab = sprintf('%s<span class="identifier">%s: %s</span> «%s»',
			($prefix ? 'konseptet ' : ''),
			$this->vocabulary->label,
			$this->notation ?: '', //$this->identifier,
			$this->prefLabel()
		);
		if ($link) {
			$lab = sprintf('<a href="%s">%s</a>', 
				URL::action('ConceptsController@getShow', [$this->vocabulary->label, $this->identifier]),
				$lab);
		}
		if ($this->draft) {
			$lab = '<abbr title="Dette konseptet ble ikke funnet i originalvokabularet sitt."><i class="glyphicon glyphicon-exclamation-sign"></i></abbr> ' . $lab;
		}
		return $lab;
	}

	private function brchildren($d, $i) {
		$t = '';
		if (isset($d[$i])) {
			$t .= '<ul>';
			foreach ($d[$i] as $n) {
				$t .= '<li>' . 
					'<a href="' . URL::action('ConceptsController@getShow', ['DDK23', $n[0]]) . '">' .
					implode(' ', $n) .
					'</a>';
				$t .= $this->brchildren($d, $n[0]);
				$t .= '</li>';
			}
			$t .= '</ul>';
		}
		return $t;
	}

	public function getRelatedContent()
	{
		// TODO: move into templates!

		if ($this->vocabulary->label == 'RT') {

			$label = $this->labels()
				->where('lang','nb')
				->where('class', 'prefLabel')
				->first();

			if ($label) {
				return View::make('concepts.related', array(
					'bs_query' => 'bs.lokoeo-frase+%3D+%22' . $label->value . '%22%20AND%20bs.bibkode=%22k%22',
					'oria_query' => 'lsr20,exact,' . $label->value,
				));
			} else {
				return ''; // Søk i BIBSYS ikke mulig uten etikett
			}

		} else if ($this->vocabulary->label == 'TEK') {

			$label = $this->labels()
				->where('lang','nb')
				->where('class', 'prefLabel')
				->first();

			if ($label) {
				return View::make('concepts.related', array(
					'bs_query' => 'bs.tek-frase+%3D+%22' . $label->value . '%22',
					'oria_query' => 'lsr12,exact,' . $label->value,
				));
			} else {
				return ''; // Søk i BIBSYS ikke mulig uten etikett
			}

		} else if ($this->vocabulary->label == 'DDK23') {

			$tree = isset($this->data['broader'])
				? '<h4>Overliggende:</h4>' . $this->brchildren($this->data['broader'], $this->identifier)
				: '';

			return View::make('concepts.related_ddc', array(
				'bs_query' => 'bs.dewey+%3D+%22' . $this->identifier . '%22%20AND%20bs.bibkode=%22k%22',
				'oria_query' => 'lsr10,exact,' . $this->identifier,
				'tree' => $tree,
				'id' => $this->identifier,
			));
		}

		return '';
	}

	/**
	 * Get CQL query for a search that includes mapped concepts.
	 *
	 * @return string
	 */
	public function broadSearchCQL()
	{
		$concepts = array($this);
		foreach ($this->sourceRelationships as $rel) {
			$concepts[] = $rel->targetConcept;
		}

		$cql = array_map(function($concept) {
			return str_replace(
				array('{label}', '{identifier}'),
				array($concept->prefLabel(), $concept->identifier),
				$concept->vocabulary->bs_cql_query
			);
		}, $concepts);

		return implode(' OR ', $cql);
	}

	/**
	 * Get an OPAC search URL that includes mapped concepts.
	 *
	 * @return string
	 */
	public function broadSearchUrl()
	{
		// TODO: Lagre sentralt sted
		$baseUrl = 'http://ask.bibsys.no/ask/action/result?cmd=&kilde=biblio&sortering=sortdate-&treffPrSide=50&cql={cql}';

		$cql = $this->broadSearchCQL();

		return str_replace('{cql}', urlencode($cql), $baseUrl);
	}

}
