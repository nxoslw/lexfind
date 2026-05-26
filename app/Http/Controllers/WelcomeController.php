<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'lawyersCount' => Lawyer::count(),
            'casesCount' => 1200000, // Matching marketing stats
        ]);
    }
}
