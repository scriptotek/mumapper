<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

	public function getStats()
	{
		$x = [];
		$y = [
			[],
			[],
			[],
			[],
			[],
			[],
		];

		$res = DB::select('select * from stats order by ts');
		foreach ($res as $row) {
			$idx = -1;
			if ($row->category == 'ready' && $row->vocabulary == 'hume') {
				$idx = 0;
			} else if ($row->category == 'reviewed' && $row->vocabulary == 'hume') {
				$idx = 1;
			} else if ($row->category == 'rejected' && $row->vocabulary == 'hume') {
				$idx = 2;
			} else if ($row->category == 'ready' && $row->vocabulary == 'real') {
				$idx = 3;
			} else if ($row->category == 'reviewed' && $row->vocabulary == 'real') {
				$idx = 4;
			} else if ($row->category == 'rejected' && $row->vocabulary == 'real') {
				$idx = 5;
			}


			$ts = explode(' ', $row->ts)[0];
			if (!in_array($ts, $x)) {
				$x[] = $ts;
				$y[0][] = 0;
				$y[1][] = 0;
				$y[2][] = 0;
				$y[3][] = 0;
				$y[4][] = 0;
				$y[5][] = 0;
            }
            $arg_idx = array_search($ts, $x);

            if ($idx != -1 && $arg_idx !== false) {
				$y[$idx][$arg_idx] = $row->value;
			}

        }

		return Response::JSON([
			'x' => $x,
			'y' => $y,
		]);

	}

}
