<?php
 
namespace App\Http\Controllers;
 
use App\Capture;
use App\Pokemon;
use Illuminate\Http\Request;
 
class CaptureController extends Controller
{
    // Returns a list of all created captures for the current user. (All the pokemon the trainer has set as captured)
    public function index()
    {
        // Get url with safeURL helper function.
        $url = $this->safeURL();
        
        // Get array of all current captures.
        $captures = auth()->user()->captures->toArray();

        // Map captures array to include entries that contain both the caputure and some basic pokemon information.
        $captures = array_map(function($capture) use ($url) {
            // Pull the 'id' and 'name' of the pokemon that matches the captures 'pokemon_id' from the database.
            $pokemon = Pokemon::select('id', 'name')->where('id', $capture['pokemon_id'])->first();
            // Add a link to get full pokemon data.
            $pokemon['links'] = [
                'self' => "{$url}/api/pokemon/{$pokemon['id']}"
            ];

            // Return the new entry consisting of both the 'capture' and the 'pokemon'
            return [
                'capture' => $capture,
                'pokemon' => $pokemon
            ];
        }, $captures);
 
        // Return json response. 200 - OK
        return response()->json([
            'success' => true,
            'message' => "Pokemon captures for ".auth()->user()->name." were successfully retrieved.",
            'data' => [
                'captured_pokemon' => $captures
            ]
        ], 200);
    }
 

    // Return capture information for a specific pokemon.
    public function show($id)
    {
        // Get url with safeURL helper function.
        $url = $this->safeURL();

        // Attempt to retrieve a capture with the matching pokemon id (of the desired pokemon) from the database.
        $capture = auth()->user()->captures()->where('pokemon_id', $id)->first()->toArray();

        // Retrieve the pokemon with the provided id from the database.
        $pokemon = Pokemon::select('id', 'name')->where('id', $id)->first();
        // Attach a self link to the pokemon.
        $pokemon['links'] = [
            'self' => "{$url}/api/pokemon/{$pokemon['id']}"
        ];
 
        // If no capture existed with a pokemon_id that matched the provided id, fail.
        if (!$capture) {
            // Return json response. 400 - Bad Request
            return response()->json([
                'success' => false,
                'message' => auth()->user()->name." has not marked {$pokemon->name} as captured. No captured on file.",
            ], 400);
        }
 
        // Else, return json response. 200 - OK
        return response()->json([
            'success' => true,
            'message' => "Capture for {$pokemon->name} was successfully retrieved for trainer ".auth()->user()->name.".",
            'data' => [
                'capture' => $capture,
                'pokemon' => $pokemon
            ]
        ], 200);
    }
 

    // Create a capture by the user for the pokemon with the provided pokemon_id, marking the pokemon as captured by the user.
    public function store(Request $request)
    {
        // Get url with safeURL helper function.
        $url = $this->safeURL();

        // Validate the form data.
        $this->validate($request, [
            'pokemon_id' => 'required|integer' // Require pokemon_id to be an integer.
        ]);

        // Retrieve the pokemon with the provided pokemon_id from the database.
        $pokemon = Pokemon::select('id', 'name')->where('id', $request->pokemon_id)->first();
        // Attach a link to the pokemon's full information to the pokemon.
        $pokemon['links'] = [
            'self' => "{$url}/api/pokemon/{$pokemon['id']}"
        ];

        // Attempt to retrive any existing capture that already exists for the pokemon.
        $existing_capture = auth()->user()->captures()->where('pokemon_id', $request->pokemon_id)->first();

        // If an existing capture exists, fail.
        if ($existing_capture) {
            // Return json response. 400 - Bad Request
            return response()->json([
                'success' => false,
                'message' => auth()->user()->name." has already marked {$pokemon->name} as captured."
            ], 400);
        };

        // Create a new capture with the provided pokemon_id.
        $capture = new Capture();
        $capture->pokemon_id = $request->pokemon_id;
 
        // If the new capture is able to be saved to the database, succeed.
        if (auth()->user()->captures()->save($capture))
            // Return json response, 201 - Created
            return response()->json([
                'success' => true,
                'message' => "Capture for {$pokemon->name} was successfully created for trainer ".auth()->user()->name.".",
                'data' => [
                    'capture' => $capture->toArray(),
                    'pokemon' => $pokemon
                ]
            ], 201);
        // Else, fail.
        else
            // Return json response. 500 - Internal Server Error
            return response()->json([
                'success' => false,
                'message' => 'Capture could not be added.'
            ], 500);
    }
 

    // As captures should only be created and delete (not updated), this simply returns a message with this information.
    public function update(Request $request, $id)
    {
        // Get url with safeURL helper function.
        $url = $this->safeURL();

        // Return json response. 400 - Bad Request
        return response()->json([
            'success' => false,
            'message' => 'Captures cannot be updated. Captures can only be created or delete.',
        ], 400);
    }
 
    
    // Deletes the capture of the pokemon with the provided $id by the user, marking the pokemon as uncaptured by the user.
    public function destroy($id)
    {
        // Get the user.
        $user = auth()->user();
        // Attempt to get a capture by the user of the intended pokemon.
        $capture = $user->captures()->select('id', 'user_id', 'pokemon_id')->where('pokemon_id', $id)->first();
        // Get the name of the pokemon in question.
        $pokemon = Pokemon::select('name')->where('id', $id)->first();
 
        // If not capture of the desired pokemon exists, fail.
        if (!$capture) {
            // Return json response. 400 - Bad Request
            return response()->json([
                'success' => false,
                'message' => "{$user->name} does not have an existing capture of {$pokemon->name}, id = {$id}."
            ], 400);
        }
 
        // Else, if the found capture is able to be delete, succeed.
        if ($capture->delete()) {
            // Return json response. 200 - OK
            return response()->json([
                'success' => true,
                'message' => "Capture of {$pokemon->name} has been removed for trainer {$user->name}.",
                'data' => [
                    'capture' => $capture->toArray()
                ]
            ], 200);
        }
        // Else, fail
        else {
            // Return json response. 500 - Internal Server Error
            return response()->json([
                'success' => false,
                'message' => "The capture of {$pokemon->name} by {$user->name} could not be delete."
            ], 500);
        }
    }
}
