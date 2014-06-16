<?php

use Illuminate\Support\MessageBag;

class UsersController extends BaseController {

	/**
	 * Display login form
	 *
	 * @return Response
	 */
	public function getLogin()
	{
		if ($this->preferredFormat() == 'application/json') {
			return Response::JSON(array(
				'error' => 'unauthenticated'
			));
		}
		return View::make('users.login');
	}

	/**
	 * Do login
	 *
	 * @return mixed
	 */
	public function postLogin()
	{
		$email = Input::get('user');
		$password = Input::get('pass');

		if (Auth::attempt(array('email' => $email, 'password' => $password)))
		{
			if ($this->preferredFormat() == 'application/json') {
				return Response::JSON(array(
					'login' => 'ok'
				));
			}
			return Redirect::intended('/');

		} else {
			if ($this->preferredFormat() == 'application/json') {
				return Response::JSON(array(
					'login' => 'failed'
				));
			}
			$errors = new MessageBag();
			$errors->add('login_failed', 'Ugyldig brukernavn eller passord.');
			return Redirect::back()
				->withErrors($errors);
		}
	}

	public function getLoginUsingGoogle()
	{
		// Construct google service
		$googleService = OAuth::consumer( 'Google' );

		// Get callback code
		$code = Input::get( 'code' );

		// If a code is provided, this is a callback request from Google
		if ( !empty( $code ) ) {

			// Get the token from the callback code
			$token = $googleService->requestAccessToken( $code );

			// Send a request with it
			$userInfo = json_decode( $googleService->request( 'https://www.googleapis.com/oauth2/v1/userinfo' ), true );

			$email = $userInfo['email'];

			$user = User::where('email', $email)->first();

			if (!$user) {

				$user = new User;
				$user->name = $userInfo['name'];
				$user->email = $userInfo['email'];
				$user->save();
				$userId = $user->id;

				$account = new UserAccount;
				$account->provider = 'google';
				$account->user_id = $userId;
				$account->email = $userInfo['email'];
				$account->extras = $userInfo;
				$account->save();

			}

			$userId = $user->id;
			Auth::loginUsingId($userId, true);

			return Redirect::to('/');

		} else {

			// Redirect to Google's authorization URL
			return Redirect::to( (string)$googleService->getAuthorizationUri() );

		}
	}

	public function getLogout()
	{
		Auth::logout();
		OAuth::createStorageInstance('Session')->clearAllTokens();
		if ($this->preferredFormat() == 'text/html') {
			return Redirect::back();
		} else {
			return Redirect::JSON(array(
				'logout' => true
			));
		}
	}

	/**
	 * Display a listing of users
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$users = User::all();

		return View::make('users.index', compact('users'));
	}

	/**
	 * Show the form for creating a new user
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return View::make('users.create');
	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$validator = Validator::make($data = Input::all(), User::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		User::create($data);

		return Redirect::route('users.index');
	}

	/**
	 * Display the specified user.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		$user = User::findOrFail($id);
		echo $user->name;
		die;

		return View::make('users.show', compact('user'));
	}

	/**
	 * Show the form for editing the specified user.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$user = User::find($id);

		return View::make('users.edit', compact('user'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postUpdate($id)
	{
		$user = User::findOrFail($id);

		$validator = Validator::make($data = Input::all(), User::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$user->update($data);

		return Redirect::route('users.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		User::destroy($id);

		return Redirect::route('users.index');
	}

}