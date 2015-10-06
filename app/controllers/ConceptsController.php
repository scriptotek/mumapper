<?php


class ConceptsController extends BaseController {

	/**
	 * Display a listing of concepts
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$concepts = Concept::all();

		if ($this->preferredFormat() == 'application/json') {
			return Response::JSON($concepts);
		}
		return View::make('concepts.index', compact('concepts'));
	}

	/**
	 * Show the form for creating a new concept
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return View::make('concepts.create');
	}

	/**
	 * Store a set of concepts in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$datalist = Input::all();

		if (!isset($datalist[0])) {
			$datalist = array($datalist);
		}

		$out = [];
		foreach ($datalist as $data) {

			if (!isset($data['labels']) || !is_array($data['labels']) || count($data['labels']) <= 0) {
				$out[] = array(
					'store' => 'failed',
					'error' => 'No labels given',
				);
				continue;
			}

			// Log::debug('Updating concept ' . array_get($data, 'identifier'));

			$concept = Concept::firstOrCreate(array(
				'vocabulary_id' => array_get($data, 'vocabulary'),
				'identifier' => array_get($data, 'identifier'),
			));

			if (is_null($concept)) {
					return Response::JSON(array(
						'error' => array(
							'message' => 'Couldnt create concept: ' . array_get($data, 'identifier')
						)
					));

			}
			$concept->data = array_get($data, 'data');
			$concept->notation = array_get($data, 'notation');

			// Log::debug(' - Saving');
			if (!$concept->save()) {
				//return Redirect::route('concepts.index');
				$out[] = array(
					'store' => 'failed',
					'error' => 'validation_failed',
					'validation_errors' => $concept->getErrors()->all(),
				);
				continue;
			}

			// Log::debug(' - Removing labels');

			$concept_id = $concept->id;
			foreach ($concept->labels as $label) {
				$label->delete();
			}

			// Log::debug(' - Updated, id : ' . $concept_id);

			foreach ($data['labels'] as $l) {
				if (!$concept_id) {
					return Response::JSON(array(
						'error' => array(
							'message' => 'No concept id set: ' . array_get($data, 'identifier')
						)
					));
				}
				$label = new Label;
				$label->concept_id = $concept_id;
				$label->class = $l['role'];
				$label->lang = $l['lang'];
				$label->value = $l['value'];

				// Log::debug(' - Adding label: ' . $l['value']);

				if (!$label->save()) {
					//return Redirect::route('concepts.index');

					// Log::debug(' - Failed to save');

					$concept->delete();
					$out[] = array(
						'store' => 'failed',
						'error' => 'validation_failed',
						'draft_label' => $label->toArray(),
						'validation_errors' => $label->getErrors()->all(),
					);
					continue 2;
				}
			}

			$out[] = array(
				'store' => 'success',
				'concept_id' => $concept_id,
			);
		}

		return Response::JSON($out);

		// return Redirect::back()
		// 	->withInput()
		// 	->withErrors($concept->getErrors());
	}

	/**
	 * Display the specified concept.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($vocabulary, $id)
	{
		$id = str_replace(',', '.', $id); // TEMP WHILE TESTING WITH PHP DEV SERVER

		$concept = Concept::where('identifier', $id)
			->whereHas('vocabulary', function($q) use ($vocabulary) {
				$q->where('label', $vocabulary);
			})
			->first();

		if (!$concept) {
			return App::abort(404, 'Concept not found.');
		}

		// $concept = Concept::with('vocabulary')->findOrFail($id);

		if ($this->preferredFormat() == 'application/json') {
			return Response::JSON($concept);
		}
		return View::make('concepts.show', array(
			'concept' => $concept,
			'subtitle' => strip_tags($concept->representation(false,false))
		));
	}

	/**
	 * Show the form for editing the specified concept.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$concept = Concept::find($id);

		return View::make('concepts.edit', compact('concept'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postUpdate($id)
	{
		$concept = Concept::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Concept::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$concept->update($data);

		return Redirect::route('concepts.index');
	}

	/**
	 * Returns the index of the element with the highest value in an array
	 */
	function argmax($mylist){ 
		$maxvalue = max($mylist); 
		while(list($key, $value) = each($mylist)){ 
			if ($value == $maxvalue) return $key; 
		}
		return 0;
	} 

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		Concept::destroy($id);

		return Redirect::route('concepts.index');
	}

	public function getSearch()
	{
		$query = Input::get('q');
		$vocabulary = Input::get('vocabulary');
		$excludevocabulary = Input::get('excludevocabulary');

		// Begrunnelse for tre queries: For å få sorteringen riktig
		// orderBy vil avhenge av hvor man får treff
		// også mtp. autocomplete
		// TODO: DRY

		$results = array();

		// Query 1

		$q = DB::table('concepts')
			->join('vocabularies', 'vocabularies.id','=','concepts.vocabulary_id')
			->join('labels', 'concepts.id', '=', 'labels.concept_id')
			->whereRaw('labels.value LIKE _utf8' . DB::connection()->getPdo()->quote($query . '%') . ' COLLATE utf8_danish_ci')
			->where('labels.lang', '=', 'nb')
			->where('labels.class', '=', 'prefLabel')
			->select('concepts.id', 'concepts.identifier', 'concepts.notation', 'vocabularies.label', 'labels.value');
		if ($vocabulary) {
			$q->where('vocabulary_id', '=', $vocabulary);
		}
		if ($excludevocabulary) {
			$q->where('vocabulary_id', '!=', $excludevocabulary);
		}

		$q->orderBy('labels.value');

		$results = array_map(function($concept) {
			return array(
				'value' => $concept->value, // for autocomplete
				'id' => $concept->id,
				'vocabulary' => $concept->label,
				'identifier' => $concept->identifier,
				'label' => $concept->value,
			);
		}, $q->limit(50)->get());

		if (count($results) == 0) {

			// Query 2

			$q = DB::table('concepts')
				->join('vocabularies', 'vocabularies.id','=','concepts.vocabulary_id')
				->join('labels', function($join)
					{
						$join->on('concepts.id', '=', 'labels.concept_id')
							->where('labels.lang', '=', 'nb')
							->where('labels.class', '=', 'prefLabel');
					})
				->where('notation', 'LIKE', $query . '%')
				->select('concepts.id', 'concepts.identifier', 'concepts.notation', 'vocabularies.label', 'labels.value');
			if ($vocabulary) {
				$q->where('vocabulary_id', $vocabulary);
			}
			$q->orderBy('notation');

			$results = array_map(function($concept) {
				return array(
					'value' => $concept->notation, // for autocomplete
					'id' => $concept->id,
					'vocabulary' => $concept->label,
					'identifier' => $concept->identifier,
					'label' => $concept->value,
				);
			}, $q->limit(50)->get());

		}

		if (count($results) == 0) {

			// Query 3
			$q = DB::table('concepts')
				->join('vocabularies', 'vocabularies.id','=','concepts.vocabulary_id')
				->join('labels', function($join) use ($query)
					{
						$join->on('concepts.id', '=', 'labels.concept_id')
							->where('labels.lang', '=', 'nb')
							->where('labels.class', '=', 'prefLabel');
					})
				->where('identifier', '=', $query)
				->select('concepts.id', 'concepts.identifier', 'concepts.notation', 'vocabularies.label', 'labels.value');
			if ($vocabulary) {
				$q->where('vocabulary_id', $vocabulary);
			}
			$q->orderBy('identifier');
			
			$results = array_map(function($concept) {
				return array(
					'value' => $concept->identifier, // for autocomplete
					'id' => $concept->id,
					'vocabulary' => $concept->label,
					'identifier' => $concept->identifier,
					'label' => $concept->value,
				);
			}, $q->limit(50)->get());
		}
		return Response::JSON($results);
	}

}
