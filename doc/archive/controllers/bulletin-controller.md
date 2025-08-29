# Scrapped codes in the Bulletin Controller

## destroy()
### 1.0: initial code
    public function destroy(Bulletin $bulletin)
    {
        $this->authorize('delete', $bulletin);
        // Handler 1: cover_photo deletion 
        if ($bulletin->cover_photo && Storage::disk('public')->exists($bulletin->cover_photo)) {
            Storage::disk('public')->delete($bulletin->cover_photo);
        }

        $bulletin->delete();
        return response()->json([
            'message' => 'Bulletin has been deleted'
        ]);
    }

## restore()
### 1.0: with restorePath 
    public function restore(Bulletin $bulletin)
    {
        $storage = Storage::disk('public');
        $trashDir = 'bulletin/trash/';
        $originalDir = 'bulletin/covers';

        if ($bulletin->cover_photo) {
            $filename = basename($bulletin->cover_photo);
            $trashPath = $trashDir . $filename;
            $restorePath = $originalDir . $filename;

            if ($storage->exists($trashPath)) {
                $storage->move($trashPath, $restorePath);

                $bulletin->cover_photo = $restorePath;
            }

            $bulletin->restore();

            return response()->json([
                'message' => 'Bulletin has been restored'
            ]);
        }
    }