<?php

class CommentsController extends BaseController {

	/**
	 * Display a listing of comments
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$comments = Comment::with('createdBy')
			->whereHas('createdBy', function($q) {
				$q->where('name','!=', 'BiblioBot');
			})
			->orderBy('id','desc')
			->get();

		return View::make('comments.index', compact('comments'));
	}

	/**
	 * Show the form for creating a new comment
	 *
	 * @return Response
	 */
	public function create()
	{
		// TODO
		return View::make('comments.create');
	}

	/**
	 * Store a newly created comment in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// TODO
		// Comment::create($data);
		// return Redirect::route('comments.index');
	}

	/**
	 * Show the form for editing the specified comment.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		// TODO
		// $comment = Comment::find($id);
		// return View::make('comments.edit', compact('comment'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// TODO
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// TODO
		// Comment::destroy($id);
		// return Redirect::route('comments.index');
	}

}