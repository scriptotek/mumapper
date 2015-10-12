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

	public function isReviewed()
	{
		return !is_null($this->latestRevision->reviewed_at);
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
		return Lang::get('relationships.states')[$this->latest_revision_state];
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
			$q = strcmp(
				$a->created_at->toISO8601String(),
				$b->created_at->toISO8601String()
			);
			if ($q == 0) {
				// Sort Revision below Comment
				return strcmp(
					get_class($a),
					get_class($b)
				);
			}
			return - $q;
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
		$args = array(
			'state' => '<a href="' . URL::action('RelationshipsController@show', $this->id) . '">' . $rev->stateLabel() . '</a>',
		);
		if ($concept->id == $this->sourceConcept->id) {
			$args['target'] = $this->targetConcept->representation();
			$label = Lang::get('relationships.as_source', $args);
		} else {
			$args['source'] = $this->sourceConcept->representation();
			$label = Lang::get('relationships.as_target', $args);
		}
		return $this->reviewStateIcon() . ' ' . $label;
	}

	public function reviewStateIcon()
	{
		return '<em class="glyphicon glyphicon-link" style="color: ' . ($this->isReviewed() ? '#1BD647' : '#ccc') . '"></em>';
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
		return ($prefix ? '<a href="' . URL::action('RelationshipsController@show', $this->id) . '">relasjonen</a> ' : '') .
			$this->sourceConcept->representation(false, false) . 
			// $this->representationFrom($this->sourceConcept, $link);
			' → ' . // <em>' . $this->stateLabel() . ' match</em> 
			$this->targetConcept->representation(false, false);

	}

}
