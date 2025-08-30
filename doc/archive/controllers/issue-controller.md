# Scrapped codes in IssueController

## destroy()
### 1.0: initial code
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
### 1.1: before refactor 
    public function destroy(Issue $issue)
    {
        $this->authorize('delete', $issue);
        $storage = Storage::disk('public');
        $trashDir = 'issues/trash/';

        if (!$trashDir) {
            $storage->makeDirectory($trashDir);
        }

        if ($issue->file && $storage->exists($issue->file)) {
            $filename = basename($issue->file);
            $trashPath = $trashDir . $filename;

            $storage->move($issue->file, $trashPath);
        }

        if ($issue->thumbnail && $storage->exists($issue->thumbnail)) {
            $filename = basename($issue->thumbnail);
            $trashPath = $trashDir . $filename;

            $storage->move($issue->thumbnail, $trashPath);
        }

        $issue->delete();
        return response()->json([
            'message' => 'Issue was deleted successfully'
        ]);
    }

## forceDestroy()
### 1.0: initial code
   public function forceDestroy(Issue $issue)
    {
        $storage = Storage::disk('public');
        $trashDir = 'issues/trash/';

        if ($issue->file) {
            $filename = basename($issue->file);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->delete($trashPath);
            }
        }

        if ($issue->thumbnail) {
            $filename = basename($issue->thumbnail);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->delete($trashPath);
            }
        }


        $issue->forceDelete();
        return response()->json([
            'message' => 'Issue was permanently deleted'
        ]);
    }

## restore()
### 1.0: initial code
  public function restore(Issue $issue)
    {
        $storage = Storage::disk('public');
        $trashDir = 'issues/trash/';

        if ($issue->file) {
            $filename = basename($issue->file);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $issue->file);
            }
        }

        if ($issue->thumbnail) {
            $filename = basename($issue->thumbnail);
            $trashPath = $trashDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $issue->thumbnail);
            }
        }

        $issue->restore();
        return response()->json([
            'message' => 'Issue was restored',
            'data' =>  IssueResource::make($issue)
        ]);
    }

## archivedIndex()
### 1.0: initial code
   public function archiveIndex()
    {
        $archivedIssues = Issue::archived()->get(); # query scope in the trait 
        return response()->json($archivedIssues);
    }

## showArchived()
### 1.1: initial code
    public function showArchived($id)
    {
        try {
            $archive = Archive::where('archivable_type', 'issue')
                ->where('id', $id)
                ->firstOrFail();
            return response()->json($archive);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Can only show archived issues']);
        }
    }