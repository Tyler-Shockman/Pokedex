<?php

use Illuminate\Database\Seeder;
use App\Pokemon as PokemonTable;

class Pokemon extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!PokemonTable::all()->first()) {

            $pokemonFile = fopen(base_path('database/data/pokemon.csv'), 'r');
            if ($pokemonFile !== false) {

                $header = NULL;
                $data = [];

                while (($row = fgetcsv($pokemonFile, 1000, ',', '"')) !== false) {

                    if (!$header) {
                        $header = $row;
                        $header[0] = 'id';
                    } else {
                        $data[] = array_combine($header, $row);
                    }
                }
                fclose($pokemonFile);

                PokemonTable::insert($data);
            }
        }
        else {
            echo "Already Seeded\r\n";
        }
    }
}
