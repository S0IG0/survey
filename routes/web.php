<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API route group
$router->group(['prefix' => 'api'], function () use ($router) {
    // Matches "/api/register
    $router->post('register', 'AuthController@register');
    // Matches "/api/login
    $router->post('login', 'AuthController@login');


    $router->get('profile',
        ['middleware' => 'auth', 'uses' => 'UserController@profile']
    );
    $router->patch('update-profile',
        ['middleware' => 'auth', 'uses' => 'UserController@updateProfile']
    );


    $router->get('all-survey', 'SurveyController@allSurvey');

    $router->post('survey',
        ['middleware' => 'auth', 'uses' => 'SurveyController@createSurvey']
    );
    $router->patch('survey/{id}',
        ['middleware' => 'auth', 'uses' => 'SurveyController@updateSurvey']
    );
    $router->post('survey/{id}',
        ['middleware' => 'auth', 'uses' => 'SurveyController@activateSurvey']
    );
    $router->delete('survey/{id}',
        ['middleware' => 'auth', 'uses' => 'SurveyController@deleteSurvey']
    );
    $router->get('my-survey',
        ['middleware' => 'auth', 'uses' => 'SurveyController@mySurvey']
    );
    $router->post('add-answer-to-survey/{id}',
        ['middleware' => 'auth', 'uses' => 'SurveyController@addAnswerToSurvey']
    );


    $router->delete('answer/{id}',
        ['middleware' => 'auth', 'uses' => 'AnswerController@deleteAnswer']
    );
    $router->patch('answer/{id}',
        ['middleware' => 'auth', 'uses' => 'AnswerController@updateAnswer']
    );

    $router->post('choose-answer/{id}',
        ['middleware' => 'auth', 'uses' => 'AnswerController@chooseAnswer']
    );
    $router->delete('choose-answer/{id}',
        ['middleware' => 'auth', 'uses' => 'AnswerController@cancelChooseAnswer']
    );
});
