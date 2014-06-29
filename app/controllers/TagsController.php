<?php

class TagsController extends BaseController {

	/**
	 * Display a listing of tags
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$tags = Tag::all();

		return View::make('tags.index', compact('tags'));
	}

	/**
	 * Show the form for creating a new tag
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('tags.create');
	}

	/**
	 * Store a newly created tag in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Tag::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Tag::create($data);

		return Redirect::route('tags.index');
	}

	/**
	 * Display the specified tag.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		$tag = Tag::findOrFail($id);

		return Redirect::to(
			URL::action('RelationshipsController@index') . '?tags%5B%5D=' . $tag->id . '&format=worklist'
		);


		return View::make('tags.show', compact('tag'));
	}

	/**
	 * Show the form for editing the specified tag.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$tag = Tag::find($id);

		return View::make('tags.edit', compact('tag'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$tag = Tag::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Tag::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$tag->update($data);

		return Redirect::route('tags.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Tag::destroy($id);

		return Redirect::route('tags.index');
	}

}