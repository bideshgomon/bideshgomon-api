<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display the specified page by slug
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->published()
            ->firstOrFail();

        return Inertia::render('User/Pages/Show', [
            'page' => $page,
        ]);
    }
}

