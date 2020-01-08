<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

        /**
     * Added this helper function to protect against what I believe to be a bug in the version of laravel that I am using.
     * Running the program through 'php artisan serve' seems to cause some issue with the environment file where the 'APP_URL'
     * doesn't exist. This causes an error to be thrown. Through this function the url should default to localhost port 8000
     * (the url for php artisan serve) if this issue occures. This will help ensure the program functions when being explored.
     */
    public function safeURL()
    {
        // Try to return the APP_URL from the environment file. If not set, return localhost:8000
        try {
            return env('APP_URL', 'http://localhost:8000');
        }
        // Catch handle exceptions and instead return localhost:8000 as the url.
        catch (HandleException $e) {
            return 'http://localhost:8000';
        }
    }
}
