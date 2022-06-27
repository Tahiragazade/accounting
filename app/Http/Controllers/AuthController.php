<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','refresh', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|integer',

        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        try {

            $user = new User;
            $user->firstName = $request->input('firstName');
            $user->lastName = $request->input('lastName');
            $user->role = $request->input('role');
            $user->email = $request->input('email');
            $user->status = 1;

            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();



            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);


        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }
    public function update(Request $request,$id)
    {

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => ['email','required',Rule::unique('users')->ignore($request->id)],
            'password' => 'required|confirmed',
            'role' => 'required|integer',

        ]);
        if ($validator->fails())
        {
            return validationError($validator->errors());
        }


        try {

            $user =User::find($id);
            if($user==null){
                return notFoundError($id);
            }
            $user->firstName = $request->input('firstName');
            $user->lastName = $request->input('lastName');
            $user->email = $request->input('email');
            $user->role = $request->input('role');
            $user->status = 1;
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();


            //return successful response
            return response()->json(['user' => $user, 'message' => 'Updated'], 201);


        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([ 'message' => 'Logged Out'], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
    public function singleUser($id)
    {

        try {
            $model = User::find($id);
            if($model==null){
                return notFoundError($id);
            }
            return response()->json($model);

        } catch (\Exception $e) {

            return response()->json(['message' =>'user not found!'], 404);
        }

    }
    public function allUsers(Request $request)
    {
        $userQuery = User::query()
            ->select('*');


        if($request->has('firstName')) {
            $userQuery->where('firstName', 'like', '%'.$request->get('firstName').'%');
        }
        if($request->has('email')) {
            $userQuery->where('email', 'like', '%'.$request->get('email').'%');
        }
        if($request->has('lastName')) {
            $userQuery->where('lastName', 'like', '%'.$request->get('lastName').'%');
        }

        if($request->has('limit')&&$request->has('page')) {
            $page = $request->page;
            $limit = $request->limit;
            $offset = ($page - 1) * $limit;
            $count = count($userQuery->get());
            $users = $userQuery->limit($limit)->offset($offset)->get();
        }
        else{
            $count = count($userQuery->get());
            $users = $userQuery->get();

        }

        return response()->json(['data' => $users, 'total' => $count]);

    }
}
