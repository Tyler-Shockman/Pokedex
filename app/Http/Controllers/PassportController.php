<?php
 
namespace App\Http\Controllers;
 
use App\User;
use Illuminate\Http\Request;
 
class PassportController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
 
        $token = $user->createToken('Trainer')->accessToken;
 
        return response()->json([
            'success' => true,
            'message' => 'A trainer account was successfully created using the provided information.',
            'token' => $token
        ], 201);
    }
 
    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('Trainer')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'Provided trainer credentials were accepted and a new access token has been provided.',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'The provided information does not match any trainer credentials. Authorization denied.',
            ], 401);
        }
    }
 
    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json([
            'success' => true,
            'message' => 'Information for the desired trainer has been successfully retrieved.',
            'trainer' => auth()->user()
        ], 200);
    }
}
