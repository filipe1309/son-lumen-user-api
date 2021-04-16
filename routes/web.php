<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/users', function (Request $request) {
        $this->validate($request, [
            'name' =>     'required | max:255',
            'email' =>    'required | email | max:255 | unique:users',
            'password' => 'required | min:6 | max:16 | confirmed',
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $model = User::create($data);
        return response()->json($model, 201);
    });
});
