<?php

// use Jenssegers\Mongodb\Model as Eloquent;

class Concept extends BaseModel implements CommentableInterface {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('vocabulary_id', 'identifier');

	/**
	 * Validation rules
	 * 
	 * @var Array
	 */
	protected static $rules = array(
		'vocabulary_id' => 'required|integer|exists:vocabularies,id',
		'identifier'    => 'required|unique_with:concepts,vocabulary_id,{id}',
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

	public function isBuiltNumber()
	{
		$labels = [];
		foreach ($this->labels as $lab) {
			if ($lab->class == 'prefLabel') {
				return false;
			}
		}
		return true;
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

		$label = null;
		foreach ($this->labels as $lab) {
			if ($lab->class == 'altLabel') {
				if (is_null($label) || strlen($lab->value) < strlen($label)) {
					$label = $lab->value;

				}
			}
		}
		if (!is_null($label)) {
			return $label;
		}
		return ' (no label found) ';
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
		$lab = sprintf('%s<span class="identifier">%s: %s %s</span> «%s»',
			($prefix ? 'konseptet ' : ''),
			$this->vocabulary->label,
			($this->isBuiltNumber() ? '<span style="background: url(/icon_puzzle.png); padding-left:20px; background-position:left; background-repeat: no-repeat;"> ' : ''),
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

	public function simpleTextRepresentation()
	{
		return ($this->notation ? $this->notation . ' ' : '' ) . $this->prefLabel();
	}

	private function brchildren($d, $i) {
		$t = '';
		if (isset($d[$i])) {
			$t .= '<ul>';
			foreach ($d[$i] as $n) {
				$t .= '<li>' . 
					'<a href="' . URL::action('ConceptsController@getShow', [$this->vocabulary->label, $n[0]]) . '">' .
					implode(' ', $n) .
					'</a>';
				$t .= $this->brchildren($d, $n[0]);
				$t .= '</li>';
			}
			$t .= '</ul>';
		}
		return $t;
	}

	public function getNotes()
	{
		return array_filter(
			isset($this->data['notes']) ? $this->data['notes'] : array(),
			function($note) {
				return strpos($note, 'Lukket bemerkning') !== 0;
			}
		);
	}

	public function getRelated()
	{
		return isset($this->data['related']) ? $this->data['related'] : array();
	}

	public function getType()
	{
		return (isset($this->data['type'])) ? $this->data['type'] : 'Topic';
	}

	public function getRelatedContent()
	{
		$label = $this->labels()
				->where('lang','nb')
				->where('class', 'prefLabel')
				->first();

		$data = array(
			'concept' => $this,
		);

		if (!is_null($label)) {
			$data['pref_label'] = $label->value;

			if ($this->vocabulary->label == 'REAL') {
				$data['bs_query'] = 'bs.lokoeo-frase+%3D+%22' . $label->value . '%22%20AND%20bs.bibkode=%22k%22';
				if ($this->getType() == 'Geographic') {
					$data['oria_query'] = 'lsr17,exact,' . $label->value;
					$data['primo_field'] = 'lsr17';
				} else {
					$data['oria_query'] = 'lsr20,exact,' . $label->value;
					$data['primo_field'] = 'lsr20';
				}

			} else if ($this->vocabulary->label == 'HUME') {
				$data['bs_query'] = 'bs.humord+%3D+%22' . $label->value . '%22';
				$data['oria_query'] = 'lsr14,exact,' . $label->value;
				$data['primo_field'] = 'lsr14';

			} else if ($this->vocabulary->label == 'WDNO') {
				$data['bs_query'] = 'bs.dewey+%3D+%22' . $this->identifier . '%22%20AND%20bs.bibkode=%22k%22';
				$data['oria_query'] = 'lsr10,exact,' . $this->identifier;
				$data['primo_field'] = 'lsr10';
			}
		}

		return View::make('concepts.related', $data);
	}

	public function extendedRepresentation($relId, $role=null)
	{
		$relationships = array_merge(
		 	$this->sourceRelationships->all(),
		 	$this->targetRelationships->all()
		);

		$relationships = array_filter($relationships, function($rel) use ($relId) {
			return $rel->id != $relId;
		});

		$tree = isset($this->data['broader'])
				? $this->brchildren($this->data['broader'], $this->identifier)
				: '';

		return View::make('concepts.extended_representation', array(
			'concept' => $this,
			'tree' => $tree,
			'role' => $role,
			'notes' => $this->getNotes(),
			'related' => $this->getRelated(),
			'otherRelationships' => $relationships,
		));
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
		$baseUrl = 'http://ask.bibsys.no/ask/action/result?cmd=&amp;kilde=biblio&amp;sortering=sortdate-&amp;treffPrSide=50&amp;cql={cql}';

		$cql = $this->broadSearchCQL();

		return str_replace('{cql}', urlencode($cql), $baseUrl);
	}

}
