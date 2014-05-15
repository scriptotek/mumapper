<?php

// use Jenssegers\Mongodb\Model as Eloquent;

/*
      {{ $rev->parent ? $rev->parent->state . '  → ' : '' }}
      {{ $rev->state }}
      <strong>{{ $rev->comment }}</strong>
      <em style="color:#666;">{{$rev->user->name}}</em>
      {{ $rev->created_at->diffForHumans() }}
*/

class Relationship extends BaseModel implements CommentableInterface {

	protected static $skosMap = array(
		'exact' => 'skos:exactMatch',
		'close' => 'skos:closeMatch',
		'broad' => 'skos:broadMatch',
		'narrow' => 'skos:narrowMatch',
		'related' => 'skos:relatedMatch',
	);

	public static $stateLabels = array(
		'suggested' => 'foreslått',
		'exact' => 'ekvivalens (EQ)',
		'close' => 'nær ekvivalens (EQ~)',
		'broad' => 'har overordnet (BM)',
		'narrow' => 'har underordnet (NM)',
		'related' => 'relatert (RM)',
		'rejected' => 'avslått',
	);

	/**
     * Validation rules
     * 
     * @var Array
     */
	protected static $rules = array(
		'source_concept_id' => 'required|integer|exists:concepts,id|unique_with:relationships,target_concept_id',
		'target_concept_id' => 'required|integer|exists:concepts,id',
	);

	public function comments()
	{
		return $this->morphMany('Comment', 'commentable');
	}

	public function revisions()
	{
		return $this->hasMany('RelationshipRevision')
			->orderBy('id', 'desc');
	}

	public function tags()
	{
		return $this->belongsToMany('Tag');
	}

	public function tagIds()
	{
		// TODO: find a better way
		$ids = array();
		foreach (DB::select('SELECT tag_id FROM relationship_tag WHERE relationship_id=?', array($this->id)) as $row) {
			$ids[] = $row->tag_id;
		}
		return $ids;
	}

	public function latestRevision()
	{
		return $this->hasOne('RelationshipRevision', 'id', 'latest_revision_id');
	}

	public function sourceConcept()
	{
		return $this->hasOne('Concept', 'id', 'source_concept_id');
	}

	public function targetConcept()
	{
		return $this->hasOne('Concept', 'id', 'target_concept_id');
	}

	public function stateLabel()
	{
		return self::$stateLabels[$this->latest_revision_state];
	}

	public function stateAsSkos()
	{
		return isset(self::$skosMap[$this->latest_revision_state])
			? self::$skosMap[$this->latest_revision_state]
			: null;
	}


	public function rdfRepresentation() {
		EasyRdf_Namespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');
		//EasyRdf_TypeMapper::set('skos:Concept', 'Concept');

		$graph = new EasyRdf_Graph();
		
		$predicate = $this->stateAsSkos();

		// Exclude suggested and rejected in RDF representation
		if ($predicate) {
			$source = $graph->resource($this->sourceConcept->uri(), 'skos:Concept');
			$target = $graph->resource($this->targetConcept->uri());

			$source->set($predicate, $target);

			foreach ($this->sourceConcept->labels as $lab) {
				$source->addLiteral('skos:' . $lab->class, $lab->value, $lab->lang);
			}
		}

		return $graph->serialise('rdfxml');
	}

	public function history()
	{
		$events = [];
		foreach ($this->comments as $e) {
			$events[] = $e;
		}
		foreach ($this->revisions as $e) {
			$events[] = $e;
		}
		usort($events, function($a, $b) {
			return - strcmp(
				$a->created_at->toISO8601String(),
				$b->created_at->toISO8601String()
			);
		});

		return array_map(function($evt) {
			return $evt->asEvent(false);
		}, $events);
	}

	/*
	 * Return a represenation as it should be displayed from
	 * a given $concept
	 */
	public function representationFrom(Concept $concept)
	{
		$rev = $this->latestRevision;
		if (!$rev) {
			return ' <span class="text-danger">[FEIL: Ingen revisjoner funnet for relasjon #' . $this->id . ']</span> ';
		}
		if ($concept->id == $this->sourceConcept->id) {
			return '<a href="' . URL::action('RelationshipsController@getEdit', $this->id) . '">' . $rev->stateLabel() . '</a> til ' . $this->targetConcept->representation(); 
		} else {
			return '<a href="' . URL::action('RelationshipsController@getEdit', $this->id) . '">' . $rev->stateLabel() . '</a> fra ' . $this->sourceConcept->representation(); 
		}
	}

	/*
	 * Return a symmetric represenation
	 */
	public function representation($prefix = true, $link = true)
	{
		$rev = $this->latest_revision_id;
		if (!$rev) {
			return $this->sourceConcept->representation($link) . 
				' &lt; ERROR: No revision exists! &gt; ' .
				$this->targetConcept->representation($link);
		}
		return ($prefix ? '<a href="' . URL::action('RelationshipsController@getEdit', $this->id) . '">relasjonen</a> ' : '') .
			$this->sourceConcept->representation(false, false) . 
			// $this->representationFrom($this->sourceConcept, $link);
			' → ' . // <em>' . $this->stateLabel() . ' match</em> 
			$this->targetConcept->representation(false, false);

	}

}
