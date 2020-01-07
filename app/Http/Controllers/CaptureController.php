<?php
 
namespace App\Http\Controllers;
 
use App\Capture;
use App\Pokemon;
use Illuminate\Http\Request;
 
class CaptureController extends Controller
{
    public function index()
    {
        $url = $this->safeURL();
        
        $captures = auth()->user()->captures->toArray();

        $captures = array_map(function($capture) use ($url) {
            $pokemon = Pokemon::select('id', 'name')->where('id', $capture['pokemon_id'])->get();
            $pokemon = $pokemon[0];
            $pokemon['links'] = [
                'self' => "{$url}/api/pokemon/{$pokemon['id']}"
            ];

            return [
                'id' => $capture['id'],
                'pokemon_id' => $capture['pokemon_id'],
                'pokemon' => $pokemon
            ];
        }, $captures);
 
        return response()->json([
            'success' => true,
            'message' => "Pokemon captures for ".auth()->user()->name." were successfully retrieved.",
            'data' => [
                'captures' => $captures
            ]
        ], 200);
    }
 
    public function show($id)
    {
        $url = $this->safeURL();

        $capture = auth()->user()->captures()->where('pokemon_id', $id)->get()->toArray();
        $capture = $capture[0];

        $pokemon = Pokemon::select('id', 'name')->where('id', $id)->get();
        $pokemon = $pokemon[0];
        $pokemon['links'] = [
            'self' => "{$url}/api/pokemon/{$pokemon['id']}"
        ];
 
        if (!$capture) {
            return response()->json([
                'success' => false,
                'message' => auth()->user()->name." has not marked {$pokemon->name} as captured. No captured on file.",
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'message' => "Capture for {$pokemon->name} was successfully retrieved for trainer ".auth()->user()->name.".",
            'data' => [
                'capture' => $capture,
                'pokemon' => $pokemon
            ]
        ], 200);
    }
 
    public function store(Request $request)
    {
        $url = $this->safeURL();

        $this->validate($request, [
            'pokemon_id' => 'required|integer'
        ]);

        $pokemon = Pokemon::select('id', 'name')->where('id', $request->pokemon_id)->get();
        $pokemon = $pokemon[0];
        $pokemon['links'] = [
            'self' => "{$url}/api/pokemon/{$pokemon['id']}"
        ];

        $existing_capture = auth()->user()->captures()->where('pokemon_id', $request->pokemon_id)->first();

        if ($existing_capture) {
            return response()->json([
                'success' => false,
                'message' => auth()->user()->name." has already marked {$pokemon->name} as captured."
            ], 400);
        };

        $capture = new Capture();
        $capture->pokemon_id = $request->pokemon_id;
 
        if (auth()->user()->captures()->save($capture))
            return response()->json([
                'success' => true,
                'message' => "Capture for {$pokemon->name} was successfully created for trainer ".auth()->user()->name.".",
                'data' => [
                    'capture' => $capture->toArray(),
                    'pokemon' => $pokemon
                ]
            ], 201);
        else
            return response()->json([
                'success' => false,
                'message' => 'Capture could not be added.'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        $url = $this->safeURL();

        return response()->json([
            'success' => false,
            'message' => 'Captures cannot be updated. Captures can only be created or delete.',
        ], 400);
    }
 
    public function destroy($id)
    {
        $user = auth()->user();
        $capture = $user->captures()->select('id', 'user_id', 'pokemon_id')->where('pokemon_id', $id)->first();
        $pokemon = Pokemon::select('name')->where('id', $id)->first();
 
        if (!$capture) {
            return response()->json([
                'success' => false,
                'message' => "{$user->name} does not have an existing capture of {$pokemon->name}, id = {$id}."
            ], 400);
        }
 
        if ($capture->delete()) {
            return response()->json([
                'success' => true,
                'message' => "Capture of {$pokemon->name} has been removed for trainer {$user->name}.",
                'data' => [
                    'capture' => $capture->toArray()
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "The capture of {$pokemon->name} by {$user->name} could not be delete."
            ], 500);
        }
    }
}
