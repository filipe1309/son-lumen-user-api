<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\Client;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\AccountCreated;
use Illuminate\Support\Facades\Notification;
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
            'redirect' => 'url',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->verification_token = md5(Str::random(16));
        $user->save();
        $redirect = route('verification_account', [
            'token' => $user->verification_token,
            'redirect' => $request->get('redirect')
        ]);
        Notification::send($user, new AccountCreated($user, $redirect));

        return response()->json($user, 201);
    });

    $router->get('/verification-account/{token}', ['as' => 'verification_account', function (Request $request, $token) {
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = true;
        $user->verification_token = null;
        $user->save();
        $redirect = $request->get('redirect');

        return redirect()->to($redirect);
    }]);


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


    // Restric Area
    $router->group(['middleware' => ['auth', 'is-verified', 'token-expired']], function () use ($router) {
        $router->get('/clients', function (Request $request) {
            return Client::all();
        });

        $router->get('/user-auth', function (Request $request) {
            return $request->user();
        });
    });
});
