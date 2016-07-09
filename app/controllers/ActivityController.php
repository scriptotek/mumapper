<?php

class ActivityController extends BaseController {

	/**
	 * Display a listing of activity
	 *
	 * @param  int  $user_id
	 * @return Response
	 */
	public function getIndex($user_id = NULL)
	{

		$query = DB::table('activity')
			->orderBy('created_at', 'desc')
			->limit(50);

		if ($user_id) {
			$query->where('created_by', $user_id);
		}

		$results = $query->paginate(50);

		// Group by model
		$grouped = array();
		foreach ($results as $r) {
			if (!isset($grouped[$r->activity_model])) {
				$grouped[$r->activity_model] = array();
			}
			$grouped[$r->activity_model][] = $r->activity_id;
		}

		// Retrieve instances
		$events = array();
		foreach ($grouped as $model => $ids) {
			foreach ($model::whereIn('id', $ids)->get() as $inst) {
				$events[] = $inst;
			}
		}

		// Sort
		usort($events, function($a, $b) {
			return - strcmp(
				$a->created_at->toISO8601String(),
				$b->created_at->toISO8601String()
			);
		});

		return View::make('activity.index', array(
			'events' => $events,
			'results' => $results,
		));
	}

	/**
	 * Display a listing of comments
	 *
	 * @param  int  $user_id
	 * @return Response
	 */
	public function getComments()
	{

		$query = Comment::orderBy('created_at', 'desc');

		$include = Input::get('include');
		$exclude = Input::get('exclude');
		$querystring = array();

		if (is_array($include)) {
			$querystring['include'] = array_filter($include, 'strlen');
			foreach ($include as $c) {
				if (!empty($c)) $query->where('content', 'LIKE', '%' . $c . '%');
			}
		}

		if (is_array($exclude)) {
			$querystring['exclude'] = array_filter($exclude, 'strlen');
			foreach ($exclude as $c) {
				if (!empty($c)) $query->where('content', 'NOT LIKE', '%' . $c . '%');			
			}			
		}

		$comments = $query->paginate(50);

		return View::make('activity.comments', array(
			'comments' => $comments,
			'total' => $comments->getTotal(),
			'include' => $include,
			'exclude' => $exclude,
			'querystring' => $querystring,
		));
	}

}
