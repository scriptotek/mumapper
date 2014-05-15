<?php

use Carbon\Carbon;

class RelationshipsController extends BaseController {

	protected $defaultSourceVocabulary = 1;

	protected function getQueryBuilder()
	{

		$reviewStates = array(
			'all' => 'alle',
			'pending' => 'venter på godkjenning',
			'reviewed' => 'godkjent',
		);
		$selectedReviewState = Input::get('reviewstate', 'all');

		$sourceVocabulary = Vocabulary::find($this->defaultSourceVocabulary);

		$targetVocabularies = Input::get('targetVocabularies');
		if ($targetVocabularies) {
			foreach ($targetVocabularies as $voc) {
				$voc = Vocabulary::find($voc);
				if (!$voc) {
					return App::abort(404, 'Invalid vocabulary given.');
				}
			}
		}

		$selectedStates = Input::get('states');
		if ($selectedStates) {
			foreach ($selectedStates as $state) {
				if (!isset(Relationship::$stateLabels[$state])) {
					return App::abort(404, 'Invalid relationship state given.');
				}
			}
		}

		$vocabularyList = [];
		foreach (Vocabulary::all() as $v) {
			if ($v->id != $sourceVocabulary->id) {
				$vocabularyList[$v->id] = $v->label;
			}
		}

		$tags = [];
		foreach (Tag::all() as $t) {
			$tags[$t->id] = $t->label;
		}
		$selectedTags = Input::get('tags');
		if ($selectedTags) {
			foreach ($selectedTags as &$tag) {
				$tag = intval($tag);
				if (!isset($tags[$tag])) {
					return App::abort(404, 'Invalid tag given.');
				}
			}
		}

		$tagsOp = array('or' => 'minst en av', 'and' => 'alle');
		$selectedTagsOp = Input::get('tagsOp', 'or');

		$labelText = Input::get('label');

		$notation = Input::get('notation');

		// With this eager loading, we get 6 queries instead of
		// 3^N queries for N relationships.
		$builder = Relationship::with([
			//'latestRevision',
			'tags',
			'sourceConcept',
			'sourceConcept.vocabulary',
			'sourceConcept.labels' => function ($query)
			{
				$query->where('class', 'prefLabel');
			},
			'targetConcept',
			'targetConcept.vocabulary',
			'targetConcept.labels' => function ($query)
			{
				$query->where('class', 'prefLabel');
			},
		]);

		if (is_array($selectedStates)) {
			$builder->whereIn('latest_revision_state', $selectedStates);
		}

		if (is_array($targetVocabularies)) {
			$builder->whereHas('targetConcept', function ($q) use ($targetVocabularies) {
				$q->whereIn('vocabulary_id', $targetVocabularies);
			});
		}

		if ($selectedTags) {
			if ($selectedTagsOp == 'and') {
				foreach ($selectedTags as $selectedTag) {
					$builder->whereHas('tags', function ($q) use ($selectedTag) {
						$q->where('tag_id', $selectedTag);
					});
				}
			} else {
				$builder->whereHas('tags', function ($q) use ($selectedTags) {
					$q->whereIn('tag_id', $selectedTags);
				});
			}
		}

		if ($labelText) {
			$builder->whereHas('sourceConcept', function ($q) use ($labelText) {
				$q->whereHas('labels', function($q)  use ($labelText) {
					$q->where('value', 'LIKE', $labelText);
				});
			})->orWhereHas('targetConcept', function ($q) use ($labelText) {
				$q->whereHas('labels', function($q)  use ($labelText) {
					$q->where('value', 'LIKE', $labelText);
				});
			});
		}

		if ($notation) {
			$builder->whereHas('targetConcept', function ($q) use ($notation) {
				$q->where('notation', 'LIKE', $notation);
			});
		}

		if ($selectedReviewState == 'pending') {
			$builder->whereHas('latestRevision', function ($q) {
				$q->whereNull('reviewed_at');
			});
			$builder->where('latest_revision_state', '!=', 'suggested');
			$builder->where('latest_revision_state', '!=', 'rejected'); // ???

		} else if ($selectedReviewState == 'reviewed') {
			$builder->whereHas('latestRevision', function ($q) {
				$q->whereNotNull('reviewed_at');
			});
		}


		//$builder->orderBy('weight', 'desc');
		$builder->orderBy('id', 'desc');

		$args = [
			'sourceVocabulary' => $sourceVocabulary,
			'vocabularyList' => $vocabularyList,
			'targetVocabularies' => $targetVocabularies,
			'states' => Relationship::$stateLabels,
			'selectedStates' => $selectedStates,
			'reviewStates' => $reviewStates,
			'selectedReviewState' => $selectedReviewState,
			'tags' => $tags,
			'selectedTags' => $selectedTags,
			'tagsOp' => $tagsOp,
			'selectedTagsOp' => $selectedTagsOp,
			'label' => $labelText,
			'notation' => $notation,
		];

		return array($args, $builder);
	}

	/**
	 * Display a listing of relationships
	 *
	 * @return Response
	 */
	public function getIndex()
	{

		list($args, $builder) = $this->getQueryBuilder();
		//$builder->orderBy('created_at')

		// Debug:
		//
		// dd([
		//	'query' => $builder->toSql(),
		//	'bindings' => $builder->getBindings()
		// ]);



		// Cache for 10 minutes?
		// $builder->remember(10);

		$format = Input::get('format', 'worklist');
		$query = Input::all();

		if (in_array($format, array('worklist', 'inline-turtle', 'inline-rdfxml'))) {
			// Limit
			//$builder->take(200);
			$relationships = $builder->paginate(1000);
		} else {
			$relationships = $builder->get();
		}

		$args['relationships'] = $relationships;

		switch ($format)
		{
			case 'inline-turtle':
				$query['format'] = 'turtle';
				$args['directLink'] = './?' . http_build_query($query);
				$args['data'] = $this->rdfResponse($relationships, 'turtle');
				return Response::view('relationships.turtle', $args);

			case 'inline-rdfxml':
				$query['format'] = 'rdfxml';
				$args['directLink'] = './?' . http_build_query($query);
				$args['data'] = $this->rdfResponse($relationships, 'rdfxml');
				return Response::view('relationships.rdfxml', $args);

			case 'turtle':
				return Response::make($this->rdfResponse($relationships, 'turtle'))
					->header('Content-Type', 'text/turtle; charset=UTF-8');

			case 'rdfxml':
				return Response::make($this->rdfResponse($relationships, 'rdfxml'))
					->header('Content-Type', 'application/rdf+xml; charset=UTF-8');

			case 'json':
				return Response::JSON($relationships);

			default:
				$args['query'] = $query;
				return Response::view('relationships.html', $args);

		}
	}

	// TODO: Move into some smarter place, maybe a service provider..
	// Or could we use the EasyRDF model mapper? Not straightforward since
	// we can't inherit both Eloquent and EasyRdf_Resource as PHP is
	// a single inheritance language
	public function rdfResponse($relationships, $serializationFormat)
	{
		EasyRdf_Namespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');
		//EasyRdf_TypeMapper::set('skos:Concept', 'Concept');

		$graph = new EasyRdf_Graph();
		
		foreach ($relationships as $rel) {

			$predicate = $rel->stateAsSkos();

			// Exclude suggested and rejected in RDF representation
			if ($predicate) {
				$source = $graph->resource($rel->sourceConcept->uri(), 'skos:Concept');
				$target = $graph->resource($rel->targetConcept->uri());

				$source->set($predicate, $target);

				foreach ($rel->sourceConcept->labels as $lab) {
					$source->addLiteral('skos:' . $lab->class, $lab->value, $lab->lang);
				}
			}

		}

		# Finally output the graph
		return $graph->serialise($serializationFormat);
	}

	/**
	 * Display the specified relationship.
	 *
	 * @param  int	$id
	 * @return Response
	 */
	public function getShow($id)
	{
	// if rdf, then 
				// return Response::make($this->rdfRepresentation())
				//	->header('Content-Type', 'application/rdf+xml; charset=UTF-8');

		$rel = Relationship::find($id);

		return View::make('relationships.show', array(
			'relationship' => $rel
		));
	}

	/**
	 * Show the form for creating a new relationship
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$sourceVocabulary = Vocabulary::find($this->defaultSourceVocabulary);

		$vocabularyList = [];
		foreach (Vocabulary::all() as $v) {
			if ($v->id != $sourceVocabulary->id) {
				$vocabularyList[$v->id] = $v->label;
			}
		}

		$targetVocabulary = null;
		$targetConcept = null;

		return View::make('relationships.create', array(
			// 'targetVocabulary' => $targetVocabulary,
			'vocabularyList' => $vocabularyList,
			'states' => Relationship::$stateLabels,
			// 'targetConcept' => $targetConcept,
		));
	}

	/**
	 * Lookup concept by vocabulary + (identifier or label)
	 *
	 * @return array(int $concept_id, bool $created)
	 */
	public function lookupConcept($cc, $createIfNotFound = false)
	{
		if (!is_array($cc)) {
			return array($cc, false);
		}
		$v = Vocabulary::where('label', $cc['vocabulary'])->first();
		if (!$v) {
			return array(null, false); // Hvis vokabularet ikke finnes gir vi opp
		}
		if (isset($cc['identifier'])) {

			$c = Concept::where('identifier', $cc['identifier'])
				->where('vocabulary_id', $v->id)->first();

			if (!$c && $createIfNotFound) {

				$c = new Concept;
				$c->vocabulary_id = $v->id;
				$c->identifier = $cc['identifier'];

				// Mark as draft! Not verified exernally
				$c->draft = true;
				$c->save();

				return array($c->id, true);
			}

		} else {

			$c = Concept::whereHas('labels', function($q) use ($cc) {
				$q->where('value', $cc['label'])->where('class', 'prefLabel')->where('lang', 'nb');
			})->where('vocabulary_id', $v->id)->first();

		}
		if ($c) {
		   return array($c->id, false);
		} else {
			return array(null, false);
		}
	}

	/**
	 * Store a newly created relationship in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$datalist = Input::all();

		if (!isset($datalist[0])) {
			$datalist = array($datalist);
		}

		$user_id = Auth::user()->id;

		$out = array();

		foreach ($datalist as $data) {

			$o = array('warnings' => array());

			list($concept, $created) = $this->lookupConcept(array_get($data, 'source_concept'));
			if (is_null($concept)) {
				$out[] = array(
					'store' => 'failed',
					'error' => 'source_concept_not_found',
				);
				continue;
			}
			$source_concept_id = $concept;
 
			list($concept, $created) = $this->lookupConcept(array_get($data, 'target_concept'), true);
			if (is_null($concept)) {
				$out[] = array(
					'store' => 'failed',
					'error' => 'target_concept_not_found',
				);
				continue;
			}
			$target_concept_id = $concept;
			if ($created) {
				$o['warnings'][] = 'target_concept_created';
			}

			$weight = isset($data['weight'])
				? floatval($data['weight'])
				: null;

			$rel = Relationship::where('source_concept_id', $source_concept_id)
				->where('target_concept_id', $target_concept_id)->first();

			$isNew = false;
			if ($rel) {

				// Add/update weight:
				if (!is_null($weight)) {
					$rel->weight = $weight;
					$rel->save();
				}

			} else {

				$isNew = true;

				// Create relationship
				$rel = new Relationship;
				$rel->source_concept_id = $source_concept_id;
				$rel->target_concept_id = $target_concept_id;
				$rel->weight = $weight;

				if (!$rel->save()) {
					$out[] = array(
						'store' => 'failed',
						'error' => 'validation_failed',
						'validation_errors' => $rel->getErrors()->all(),
					);
					continue;
				}
			}
			$rel_id = $rel->id;

			// Add tags
			$tags = array_get($data, 'tags');
			$existingTags = $rel->tagIds();

			if ($tags) {
				foreach ($tags as $tag) {
					$t = Tag::where('label', $tag)->first();
					if (!$t) {
						if ($isNew) $rel->delete(); // TODO: Use transaction and rollback instead
						$out[] = array(
							'store' => 'failed',
							'error' => 'tag_not_found',
							'tag' => $tag,
						);
						continue(2);
					}
					if (!in_array($t->id, $existingTags)) {
						$rel->tags()->attach($t->id);
					}
				}
			}

			// Add revision
			if (is_null($rel->latest_revision_id) || isset($data['comment'])) {
				$rev = new RelationshipRevision;
				$rev->relationship_id = $rel_id;
				$rev->parent_revision = $rel->latest_revision_id; // Can be null, but that's ok
				$rev->created_by = $user_id;
				$rev->state = array_get($data, 'state');
				if (!$rev->save()) {
					//return Redirect::route('concepts.index');
					if ($isNew) $rel->delete(); // TODO: Use transaction and rollback instead
					$out[] = array(
						'store' => 'failed',
						'error' => 'validation_failed',
						'validation_errors' => $rev->getErrors()->all(),
					);
					continue;
				}
			}

			// Add comment
			if (isset($data['comment'])) {

				// Add comment
				$com = new Comment;
				$com->commentable_id = $rev->id;
				$com->commentable_type = 'RelationshipRevision';
				$com->created_by = $user_id;
				$com->content = $data['comment'];
				$com->save();

			}

			// Response
			$o['store'] = 'success';
			$o['relationship_id'] = $rel_id;
			$out[] = $o;
		}

		if ($this->preferredFormat() == 'application/json') {
			return Response::JSON($out);
		}

		return Redirect::action('RelationshipsController@getEdit', $rel->id);

	}

	/**
	 * Show the form for editing the specified relationship.
	 *
	 * @param  int	$id
	 * @return Response
	 */
	public function getEdit($id)
	{

		$query = '?' . http_build_query(Input::all());
		list($args, $builder) = $this->getQueryBuilder();


		// Find next item as item with lower id (since we order by id desc)
		$next = $builder->where('id','<',$id)->first();

		$rel = Relationship::with([
			'latestRevision',
			'sourceConcept',
			'targetConcept',
			'revisions',
			'revisions.comments',
			'comments',
			'comments.createdBy',
		])->findOrFail($id);

		return View::make('relationships.edit', array(
			'relationship' => $rel,
			'states' => Relationship::$stateLabels,
			'query' => $query,
			'next' => $next,
			'canReview' => (is_null($rel->latestRevision->reviewed_at) && $rel->latestRevision->created_by != Auth::user()->id) ? 'true' : 'false',
		));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int	$id
	 * @return Response
	 */
	public function postUpdate($id)
	{
		$rel = Relationship::findOrFail($id);

		$currentRev = $rel->latestRevision;

		if (Input::get('state') == $currentRev->state) {
			// Review
			if (Auth::user()->id == $currentRev->created_by) {
				// TODO: Redirect::back() med en fin feilmelding
				die('Dude, du kan ikke godkjenne egne revisjoner');
			}

			$newRev = new RelationshipRevision;
			$newRev->state = Input::get('state');
			$newRev->created_by = Auth::user()->id;
			$newRev->reviewed_at = Carbon::now();
			$newRev->parent_revision = $currentRev->id;
			if (!$rel->revisions()->save($newRev)) {
				return Redirect::back()
					->withErrors($newRev->getErrors())
					->withInput();
			}

		} else {
			$newRev = new RelationshipRevision;
			$newRev->state = Input::get('state');
			$newRev->created_by = Auth::user()->id;
			$newRev->parent_revision = $currentRev->id;
			if (!$rel->revisions()->save($newRev)) {
				return Redirect::back()
					->withErrors($newRev->getErrors())
					->withInput();
			}

			// MERK: Vi har en MySQL TRIGGER som oppdaterer $rel->latest_revision_id,
			// så vi slipper å tenke på det
		}

		if (Input::get('comment')) {
			$comm = new Comment;
			$comm->commentable_id = $newRev->id;
			$comm->commentable_type = 'RelationshipRevision';
			$comm->content = Input::get('comment');
			$comm->created_by =  Auth::user()->id;
			$newRev->comments()->save($comm);
		}

		return Redirect::action('RelationshipsController@getEdit', $rel->id);
	}

	/**
	 * Store a new comment
	 *
	 * @return Response
	 */
	public function postAddComment($id)
	{
		$rel = Relationship::findOrFail($id);

		if (empty(Input::get('comment'))) {
			die('det er vel ingen vits å sende inn en tom kommentar, vel?');
		}
		$comm = new Comment;
		$comm->commentable_id = $rel->id;
		$comm->commentable_type = 'Relationship';
		$comm->content = Input::get('comment');
		$comm->created_by =  Auth::user()->id;
		$rel->comments()->save($comm);

		return Redirect::action('RelationshipsController@getEdit', $rel->id);		
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int	$id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		Relationship::destroy($id);

		return Redirect::route('relationships.index');
	}

}
