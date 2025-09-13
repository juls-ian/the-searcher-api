<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Services\SearchService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SearchController extends Controller
{
    // Instance of SearchService
    public function __construct(protected SearchService $searchService) {}

    /**
     * Universal search 
     */
    public function universal(Request $request)
    {
        $query = $request->input('q'); # q = query param | /api/search/universal?q=university

        if (! $query) {
            return response()->json(['message' => 'Missing query parameter'], 422);
        }

        $results = $this->searchService->universalSearch($query, $request->input('per_page', 10));

        return response()->json($results);
    }

    /**
     * Model search 
     */
    public function model(Request $request, string $model)
    {
        $query = $request->input('q');

        if (! $query) {
            return response()->json(['message' => 'Missing query parameter'], 422);
        }

        try {
            $results = $this->searchService->modelSearch($model, $query, $request->input('per_page', 10));
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json($results);
    }

    /**
     * Search/filter archive
     */
    public function archive(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string'],
            'year' => ['nullable', 'integer'],
            'month' => ['nullable', 'string'],
            'sort' => ['nullable', 'in:asc,desc'],
            // Support year only and full date
            'from' => ['nullable', 'regex:/^\d{4}(-\d{2}-\d{2})?$/'],
            'to' => ['nullable', 'regex:/^\d{4}(-\d{2}-\d{2})?$/'],
        ]);

        $results = $this->searchService->searchArchives($validated);
        return response()->json($results);
    }
}