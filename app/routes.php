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

Route::filter('force.ssl', function()
{
	if(!Request::secure())
	{
		return Redirect::secure(Request::getRequestUri());
	}
});

Route::get('/', function()
{
	return Redirect::action('RelationshipsController@index');
});

Route::get('/stats', function()
{
	return Response::view('stats');
});

Route::get('/stats.json', 'HomeController@getStats');

//Route::group(array('before' => 'force.ssl'), function()
//{
	Route::get('/login', array(
		'as' => 'login',
		'uses' => 'UsersController@getLogin',
	));

	Route::post('/login', 'UsersController@postLogin');
	Route::get('/login-using-google', 'UsersController@getLoginUsingGoogle');
//});

Route::get('/concepts/RT/REAL{id}', function($id) {
	return Redirect::action('ConceptsController@getShow', array('RT', $id));
});

Route::get('/concepts/search', 'ConceptsController@getSearch');
Route::get('/concepts/{vocabulary}/{id}', 'ConceptsController@getShow');

Route::get('/activity/comments', 'ActivityController@getComments');

Route::get('/relationships/edit/{id}', function($id) {
	return Redirect::action('RelationshipsController@show', $id);
});
Route::get('/relationships/show/{id}', function($id) {
	return Redirect::action('RelationshipsController@show', $id);
});
Route::get('/tags/{id}', function($id) {
	return Redirect::action('TagsController@getShow', $id);
});
Route::get('/tags', function() {
	return Redirect::action('TagsController@getIndex');
});

Route::get('/apis/snl', 'ApiController@getSnl');
Route::get('/apis/nowiki', 'ApiController@getNowiki');

Route::get('/lists/{id}', 'TagsController@getShow')
	->where('id', '[0-9]+');

Route::get('/relationships/{id}', 'RelationshipsController@show')
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
});

Route::get('/relationships', 'RelationshipsController@index');
Route::get('/lists', 'TagsController@getIndex');
