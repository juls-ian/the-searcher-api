<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateArticleCategoryRequest;
use App\Http\Resources\ArticleCategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', ArticleCategory::class);
        // eager load relationship to Article 
        $articleCategories = ArticleCategory::with(['articles'])->get();
        return ArticleCategoryResource::collection($articleCategories);
        // return ArticleCategoryResource::collection(ArticleCategory::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ArticleCategory::class);
        $validatedData = $request->validate([
            'name' => ['required'],
            'parent_id' => ['exists:article_categories,id']
        ]);

        $category = ArticleCategory::create($validatedData);
        $category->load(['articles']); # relationship
        return ArticleCategoryResource::make($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(ArticleCategory $articleCategory)
    {
        $this->authorize('view', $articleCategory);
        return ArticleCategoryResource::make($articleCategory);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ArticleCategory $articleCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArticleCategory $articleCategory)
    {
        $this->authorize('update', $articleCategory);
        $validatedData = $request->validate([
            'name' => ['required'],
            'parent_id' => ['exists:article_category,id']
        ]);

        $articleCategory->update($validatedData); # update category 
        return ArticleCategoryResource::make($articleCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleCategory $articleCategory)
    {
        $this->authorize('delete', $articleCategory);
        $articleCategory->delete();
        return response()->json(['message' => 'Article deleted successfully'], 200);
    }
}