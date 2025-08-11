<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Http\Resources\IssueResource;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Issue::class);
        return IssueResource::collection(Issue::all());
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
    public function store(StoreIssueRequest $request)
    {
        $this->authorize('create', Issue::class);
        $validatedIssue = $request->validated();

        // Handler 1: file upload 
        if ($request->hasFile('issue_file')) {
            $filePath = $request->file('issue_file')->store('issues/files', 'public');
            $validatedIssue['issue_file'] = $filePath;
        }

        // Handler 2: thumbnail upload 
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('issues/thumbnail', 'public');
            $validatedIssue['thumbnail'] = $thumbnailPath;
        }

        // Handler 3: date/time published_at 
        if (isset($validatedIssue['published_at']) && $validatedIssue['published_at']) {

            # if date is provided 
            $validatedIssue['published_at'] = Carbon::parse($validatedIssue['published_at']);
        } else {

            $validatedIssue['published_at'] = Carbon::now();
        }

        $issue = Issue::create($validatedIssue);
        return IssueResource::make($issue);
    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issue)
    {
        return IssueResource::make($issue);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Issue $issue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        $this->authorize('update', $issue);
        $validatedIssue = $request->validated();
        $storage = Storage::disk('public');

        // Handler 1: file upload 
        if ($request->hasFile('issue_file')) {

            # delete old file if it exists 
            if ($issue->issue_file && $storage->exists($issue->issue_file)) {
                $issue->delete($issue->issue_file);
            }
            $validatedIssue['issue_file'] = $request->file('issue_file')->store('issues/files', 'public');
        } else {

            # exclude file in any subsequent db operation 
            unset($validatedIssue['issue_file']);
        }

        // Handler 2: thumbnail
        if ($request->hasFile('thumbnail')) {

            # delete existing thumbnail 
            if ($issue->thumbnail && $storage->exists($issue->thumbnail)) {
                $issue->delete($issue->thumbnail);
            }
            $validatedIssue['thumbnail'] = $request->file('thumbnail')->store('issues/thumbnails', 'public');
        } else {
            unset($validatedIssue['thumbnail']);
        }

        $issue->update($validatedIssue);
        return IssueResource::make($issue);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issue)
    {
        $this->authorize('delete', $issue);
        if ($issue->issue_file && Storage::disk('public')->exists($issue->issue_file)) {
            Storage::disk('public')->delete($issue->issue_file);
        }

        if ($issue->thumbnail && Storage::disk('public')->exists($issue->thumbnail)) {
            Storage::disk('public')->delete($issue->thumbnail);
        }

        $issue->delete();
        return response()->json([
            'message' => 'Issue was deleted successfully'
        ]);
    }
}
