<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
        ]);

        $validatedData['password'] = bcrypt($request->password);
        // $validatedData['last_login'] = date("Y-m-d H:i:s");

        //dd($validatedData);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {

        //  https://stackoverflow.com/a/64587974/3650230

        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        if (!auth()->attempt($request->all())) {
            return response(['message' => 'Invalid Credentials']);
        }

        auth()->user()->last_login = Carbon::now()->toDateTimeString();
        auth()->user()->save();

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['email' => $request->email, 'access_token' => $accessToken]);

    }
}
