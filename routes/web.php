<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = User::create($data);

        return response()->json($user, 201);
    });

    $router->post('/login', function (Request $request) {
        $this->validate($request, [
            'email' =>    'required | email',
            'password' => 'required',
        ]);

        $email = $request->get('email');
        $password = $request->get('password');
        $user = User::where('email', '=', $email)->first();
        if (!$user || Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }

        $expiration = new Carbon();
        $expiration->addHour(2);
        $user->api_token = sha1(Str::random(32)) . '.' . sha1(Str::random(32));
        $user->api_token_expiration = $expiration->format('Y-m-d H:i:s');
        $user->save();

        return [
            'api_token' => $user->api_token,
            'api_token_expiration' => $user->api_token_expiration
        ];
    });

    $router->post('/refresh-token', ['middleware' => 'auth', function () use ($router) {
        $user = Auth::user();
        $expiration = new Carbon();
        $expiration->addHour(2);
        $user->api_token = sha1(Str::random(32)) . '.' . sha1(Str::random(32));
        $user->api_token_expiration = $expiration->format('Y-m-d H:i:s');
        $user->save();

        return [
            'api_token' => $user->api_token,
            'api_token_expiration' => $user->api_token_expiration
        ];
    }]);


    $router->group(['middleware' => ['auth', 'token-expired']], function () use ($router) {
        $router->get('/clients', function (Request $request) {
            return ['ok'];
        });

        $router->get('/user-auth', function (Request $request) {
            return $request->user();
        });
    });
});
