<?php

class RelationshipRevisionsController extends BaseController {

	/**
	 * Display a listing of relation
	 *
	 * @return Response
	 */
	public function index()
	{
		$relationrevision = RelationRevision::all();

		return View::make('relationship_revisions.index', compact('relationrevision'));
	}

	/**
	 * Show the form for creating a new revision
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('relationship_revisions.create');
	}

	/**
	 * Store a newly created revision in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), RelationshipRevision::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		RelationshipRevision::create($data);

		return Redirect::route('relationship_revisions.index');
	}

	/**
	 * Display the specified revision.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$revision = RelationshipRevision::findOrFail($id);

		return View::make('relationship_revisions.show', compact('revision'));
	}

	/**
	 * Show the form for editing the specified revision.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$revision = RelationshipRevision::find($id);

		return View::make('relationship_revisions.edit', compact('revision'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$revision = RelationshipRevision::findOrFail($id);

		$validator = Validator::make($data = Input::all(), RelationshipRevision::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$revision->update($data);

		return Redirect::route('relationship_revisions.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		RelationshipRevision::destroy($id);

		return Redirect::route('relationship_revisions.index');
	}

}