<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request){
        
        $credentials = $request->only('user_name', 'password');

        $validator = Validator::make($credentials, [
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->messages(),
                ], 
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user_name = $request->input('user_name');
        $password = $request->input('password');
        
        try {
            
            $user = User::where('user_name', $user_name)
                    ->where('password', md5($password))
                    ->first();

            if(!$user) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Login credentials are invalid.',
                    ], 
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $token = auth('api')->login($user);

        } catch (JWTException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Could not create token',
                ], 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            [
                'success' => true,
                'token' => $token,
            ], 
            Response::HTTP_OK
        );
    }
}