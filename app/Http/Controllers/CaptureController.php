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
        ]);
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
        ], 400);
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
            ]);
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
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Capture could not be added.'
            ], 500);
    }
 
    public function update(Request $request, $id)
    {
        $capture = auth()->user()->captures()->find($id);
 
        if (!$capture) {
            return response()->json([
                'success' => false,
                'message' => 'Capture with id ' . $id . ' not found'
            ], 400);
        }
 
        $updated = $capture->fill($request->all())->save();
 
        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Capture could not be updated'
            ], 500);
    }
 
    public function destroy($id)
    {
        $capture = auth()->user()->captures()->find($id);
 
        if (!$capture) {
            return response()->json([
                'success' => false,
                'message' => 'Capture with id ' . $id . ' not found'
            ], 400);
        }
 
        if ($capture->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Capture could not be deleted'
            ], 500);
        }
    }
}
