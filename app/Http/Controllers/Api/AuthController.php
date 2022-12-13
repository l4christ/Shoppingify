<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * signin using username or email address
     *
     * @param  mixed $request
     * @return void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6',
            'device_name' => 'required',
        ],[
            'email.required' => 'kindly enter your username / email',
            'password.min' => 'Kindly enter a valid password'
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect credentials'
                ], 401);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Incorrect credentials'
            ], 401);
        }
        $tokenResult = $user->createToken($request->device_name);
        $token = $tokenResult->plainTextToken;
        $token = $tokenResult->accessToken;
        $token->save();
        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'success' => 'User logged in successfully',
        ]);
    }

    /**
     * signup for an account
     *
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        $rules = [

            'name' => ['required', 'min:4', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:55', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'device_name' => 'required',

        ];

        $messages = [
            'name.min' => 'Name must be at least :min characters.',
            'email' => 'Kindly provide a valid email.',
            'email.required' => 'Email field is required.',
            'password.required' => 'Password field is required.',

        ];
        $fields = $this->validate($request, $rules, $messages);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);
        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Successfully created user!',
            'token' => $token,
        ], 201);
    }
}
