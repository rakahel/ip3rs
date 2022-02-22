<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        session()->put('cilog.session_id', date('YmdHis').rand(1000,9999));
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            $result = [
                'code' => 'F0001',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages()->get('*')
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 400);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            // return response()->json(['error' => 'Unauthorized'], 401);
            $result = [
                'code' => 'R0002',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'Unauthorized'
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 401);
        }

        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'Success',
            'data' => [
                'token' => $token
            ]
        ];
        cilog()->toDb(json_encode($result));
        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            // return response()->json($validator->errors()->toJson(), 400);
            $result = [
                'code' => 'F0001',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages()->get('*')
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'User successfully registered',
            'data' => $user
        ];
        cilog()->toDb(json_encode($result));
        return response()->json($result, 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'User successfully signed out'
        ];
        cilog()->toDb(json_encode($result));
        return response()->json($result);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'Refresh token'
        ];
        cilog()->toDb(json_encode($result));
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'Success',
            'data' => auth()->user()
        ];
        cilog()->toDb(json_encode($result));
        return response()->json($result);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}

?>
