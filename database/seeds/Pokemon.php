<?php

use Illuminate\Database\Seeder;
use App\Pokemon as PokemonTable;

class Pokemon extends Seeder
{
    /**
     * Seeds the Pokemon table of the database with the information from the pokemon.csv file.
     *
     * @return void
     */
    public function run()
    {
        // Seeds the full table. If the table already has any records, don't seed.
        if (!PokemonTable::all()->first()) {

            // Try to get the pokemon.csv file and, if it is found, process the file.
            $pokemonFile = fopen(base_path('database/data/pokemon.csv'), 'r');
            if ($pokemonFile !== false) {

                // Initialize a header to store the headers that will be attached to each row of data.
                $header = NULL;
                // Initialize the data variable for storing the actual data to be inserted into the database table.
                $data = [];

                // Loop until the end of the file is reached.
                while (($row = fgetcsv($pokemonFile, 1000, ',', '"')) !== false) {
                    // Set the first row to the header.
                    if (!$header) {
                        $header = $row;
                        $header[0] = 'id'; // The code was placing a " " in front the id (" id"), fixed by overwriting this header to not include the space.
                    }
                    // Add all additional rows as data.
                    else {
                        $data[] = array_combine($header, $row);
                    }
                }
                // Close the file.
                fclose($pokemonFile);

                // Insert the data into the pokemon table.
                PokemonTable::insert($data);
            }
        }
        // If the table had no records, echo to inform user.
        else {
            echo "ERROR: Table already contains information. Can only seed empty table.\r\n";
        }
    }
}
