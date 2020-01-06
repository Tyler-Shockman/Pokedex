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
                'self' => "{$url}/pokemon/{$pokemon['id']}"
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
            'data' => $captures
        ]);
    }
 
    public function show($id)
    {
        $capture = auth()->user()->captures()->find($id);
 
        if (!$capture) {
            return response()->json([
                'success' => false,
                'message' => 'Capture with id ' . $id . ' not found'
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'data' => $capture->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $this->validate($request, [
            'pokemon_id' => 'required|integer'
        ]);
 
        $capture = new Capture();
        $capture->pokemon_id = $request->pokemon_id;
 
        if (auth()->user()->captures()->save($capture))
            return response()->json([
                'success' => true,
                'data' => $capture->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Capture could not be added'
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
