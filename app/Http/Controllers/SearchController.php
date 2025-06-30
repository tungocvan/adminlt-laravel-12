<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SearchController extends Controller
{
    public function showNavbarSearchResults(Request $request)
    {
        // Check that the search keyword is present.
        //dd($request->all());
        if (!$request->filled('searchVal')) {
            return back();
        }

        // Get the search keyword.

        $keyword = $request->input('searchVal');

        //Log::info("A navbar search was triggered with next keyword => {$keyword}");

        // TODO: Create the search logic and return adequate response (maybe a view
        // with the results).
        // ...
    }
}
