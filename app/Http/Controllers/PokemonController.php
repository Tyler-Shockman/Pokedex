<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pokemon;

class PokemonController extends Controller
{
    /**
     * Retrieves the page (number) and count (per page) query parameters from the request and returns the desired page
     * from a paginated list of pokemon that contains the desired count per page.
     */
    public function index(Request $request)
    {
        // Get page number query parameter and convert to an int. If not provided, default to first page (1).
        $page = (int)$request->input('page', 1);
        // If the provided value was less than or equal to 0, fail. All non int inputs will have converted to 0.
        if (0 >= $page)
        {
            // Return json response. Code 400 - Bad Request
            return response()->json([
                'success' => false,
                'message' => 'The provided page number is not an acceptable value. '.
                    'The page query parameter must be set an integer greater than or equal to 1.',
                'links' => [
                    'first' => "{$_ENV['APP_URL']}/api/pokemon?page=1"
                ]
                ], 400);
        }

        // Get count query parameter and convert to an int. If not provided, default to 10 items per page.
        $count = (int)$request->input('count', 10);
        // If the provided count is not 5, 10, 20, 25, 50, or 100 items per page. Fail.
        if (!in_array($count, [5, 10, 20, 25, 50, 100]))
        {
            // Retturn json response. Code 400 - Bad Request
            return response()->json([
                'success' => false,
                'message' => 'The provided page count is not an acceptable value. '.
                    'Acceptable page counts are 5, 10, 20, 25, 50, and 100',
                'links' => [
                    '5Count' => "{$_ENV['APP_URL']}/api/pokemon?count=5",
                    '10Count' => "{$_ENV['APP_URL']}/api/pokemon?count=10",
                    '20Count' => "{$_ENV['APP_URL']}/api/pokemon?count=20",
                    '25Count' => "{$_ENV['APP_URL']}/api/pokemon?count=25",
                    '50Count' => "{$_ENV['APP_URL']}/api/pokemon?count=50",
                    '100Count' => "{$_ENV['APP_URL']}/api/pokemon?count=100"
                ]
            ], 400);
        };
        
        $pokemon = Pokemon::orderBy('id', 'asc')->get();

        // If the page number/count provided would constitute a non existent page (greater than the range of pages for the count), fail.
        if ($page > $last = (ceil($pokemon->count() / $count)))
        {
            // Return json response. Code 400 - Bad Request.
            return response()->json([
                'success' => false,
                'message' => 'The provided page number does not exist for the provided page count. '.
                    'The page number is greater than the last available page.',
                'links' => [
                    'last' => "{$_ENV['APP_URL']}/api/pokemon?page={$last}&count={$count}"
                ]
                ], 400);
        }

        // Try to retrieve the pokemon for the desired page. Catch any errors.
        try {
            // dd($pokemon);
            $pokemon = array_slice(
                $pokemon->all(), 
                ($page-1)*$count, // '$page - 1' as the first page (1) would be offset 0 times, not offset once.
                $count
            );
        } catch (Exception $e) {
            // Return json response. Code 500 - Insernal Server Error.
            return response()->json([
                'success' => false,
                'message' => 'Oops, something when wrong on our end.'
            ], 500);
        }

        // Determine previous/next page. -1/+1 of current unless current is the first or last page.
        $previous = ($page == 1 ? 1 : ($page - 1));
        $next = ($page == $last ? $last : ($page + 1));

        // Map pokemon array to only include the desired information. Add link to self's full information.
        $pokemon = array_map(function($oldPokemon) {
            return [
                'id' => $oldPokemon->id,
                'name' => $oldPokemon->name,
                'types' => $oldPokemon->types,
                'description' => $oldPokemon->description,
                'links' => [
                    'self' => "{$_ENV['APP_URL']}/api/pokemon/{$oldPokemon->id}"
                ]
                ];
        }, $pokemon);

        // Return json response. 200 - OK
        return response()->json([
            'success' => true,
            'message' => "Page {$page} of the paginate list of pokemon defined with {$count} pokemon per page ".
                "was successfully retrieved.",
            'links' => [
                'first' => "{$_ENV['APP_URL']}/api/pokemon?page=1&count={$count}",
                'previous' => "{$_ENV['APP_URL']}/api/pokemon?page={$previous}&count={$count}",
                'self' => "{$_ENV['APP_URL']}/api/pokemon?page={$page}&count={$count}",
                'next' => "{$_ENV['APP_URL']}/api/pokemon?page={$next}&count={$count}",
                'last' => "{$_ENV['APP_URL']}/api/pokemon?page={$last}&count={$count}",
            ],
            'data' => [
                'page_number' => $page,
                'total_pages' =>$last,
                'count' => $count,
                'pokemon' => $pokemon
            ]
        ], 200);
    }


    /**
     * Takes the provided $id parameter and returns the information for the
     * pokemon with that id.
     */
    public function show($id)
    {
        // Query for a pokemon with the desired id.
        $pokemon = Pokemon::where('id', $id)->first();

        // If no pokemon is found, fail.
        if (!$pokemon) {
            // Return json response. 404 - Not Found
            return response()->json([
                'success' => false,
                'message' => "Could not find a pokemon with the id {$id}.",
                'links' => [
                    'PokemonListPage1' => "{$_ENV['APP_URL']}/api/pokemon?page=1&count=10"
                ]
            ], 404);
        }

        // Else, return json response. 200 - OK
        return response()->json([
            'success' => true,
            'message' => "Information for {$pokemon->name}, id = {$id}, successfully retrieved.",
            'links' => [
                'self' => "{$_ENV['APP_URL']}/api/pokemon/{$id}"
            ],
            'data' => $pokemon
        ], 200);
    }
}
