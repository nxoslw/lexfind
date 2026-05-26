<?php

namespace App\Http\Controllers;

use App\Models\LawyerCase;
use Inertia\Inertia;
use Inertia\Response;

class LawyerCaseController extends Controller
{
    /**
     * Display the specified lawyer case.
     */
    public function show(LawyerCase $case): Response
    {
        $case->load('lawyer');

        return Inertia::render('cases/Show', [
            'case' => $case,
        ]);
    }
}
