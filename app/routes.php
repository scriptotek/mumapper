<?php

use Negotiation\FormatNegotiator;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


App::missing(function(NotFoundHttpException $exception)
{

	$negotiator = new FormatNegotiator;
	$acceptHeader = $_SERVER['HTTP_ACCEPT'];

	$priorities = array('text/html', 'application/json');
	$preferredFormat = $negotiator->getBest($acceptHeader, $priorities)->getValue();

	if ($preferredFormat == 'application/json') {
		return Response::JSON(array(
			'error' => '404',
			'details' => $exception->getMessage(),
		), 404);
	}

    return Response::view('errors.missing', array(
		'message' => $exception->getMessage()
    ), 404);
});

Route::get('/', function()
{
	return Redirect::action('RelationshipsController@getIndex');
});


Route::get('/login', 'UsersController@getLogin');
Route::post('/login', 'UsersController@postLogin');
Route::get('/login-using-google', 'UsersController@getLoginUsingGoogle');

Route::get('/concepts/search', 'ConceptsController@getSearch');
Route::get('/concepts/{vocabulary}/{id}', 'ConceptsController@getShow');

Route::get('/activity/comments', 'ActivityController@getComments');

Route::get('/relationships/{id}', 'RelationshipsController@getShow')
	->where('id', '[0-9]+');

Route::group(array('before' => 'auth'), function()
{
	Route::controller('concepts', 'ConceptsController');
	Route::controller('relationships', 'RelationshipsController');
	Route::controller('relationship_revisions', 'RelationshipRevisionsController');
	Route::controller('users', 'UsersController');
	Route::controller('comments', 'CommentsController');
	Route::get('/activity/{user}', 'ActivityController@getIndex');
	Route::controller('activity', 'ActivityController');
	Route::controller('tags', 'TagsController');
});

Route::get('/relationships', 'RelationshipsController@getIndex');
