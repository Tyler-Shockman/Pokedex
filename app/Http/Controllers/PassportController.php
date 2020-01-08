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
        // Validate the provided form data.
        $this->validate($request, [
            'name' => 'required|min:3', // Require 'name' of at least 3 characters in length.
            'email' => 'required|email|unique:users',  // Require 'email' that has an email form and not is not already used within the users table.
            'password' => 'required|min:6',  // Require 'password' of at least 6 characters in length.
        ]);
 
        // Create a new user with the provided form-data.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)  // Encrypte the password for storing in the database..
        ]);
 
        // Create a new token.
        $token = $user->createToken('Trainer')->accessToken;
 
        // Return json response. 201 - Created.
        return response()->json([
            'success' => true,
            'message' => 'A trainer account was successfully created using the provided information.',
            'token' => $token // Include an initial access token.
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
        // Get form-data user credentails.
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        // If the user credentials match a user.
        if (auth()->attempt($credentials)) {
            // Create an access token for that user. 
            $token = auth()->user()->createToken('Trainer')->accessToken;
            // Return a json response. 200 - OK
            return response()->json([
                'success' => true,
                'message' => 'Provided trainer credentials were accepted and a new access token has been provided.',
                'token' => $token
            ], 200);
        } // Else, fail.
        else {
            // Return a json response. 401 - Unauthorized.
            return response()->json([
                'success' => false,
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
        // Middlewear has already authorized user. Return a json response. 200 - OK
        return response()->json([
            'success' => true,
            'message' => 'Information for the desired trainer has been successfully retrieved.',
            'trainer' => auth()->user()  // Return user information.
        ], 200);
    }
}
