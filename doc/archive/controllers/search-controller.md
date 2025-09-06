# Scrapped codes from SearchController

## search()
### 1.0: initial code
use App\Models\Article;
use App\Models\CommunitySegment;
use App\Models\User;

public function search(Request $request)
{
    $query = $request->input('q');

    $results = collect();

    $results = $results->merge(
        Article::search($query)->get()->map(fn($a) => [
            'type' => 'article',
            'data' => $a,
        ])
    );

    $results = $results->merge(
        CommunitySegment::search($query)->get()->map(fn($s) => [
            'type' => 'segment',
            'data' => $s,
        ])
    );

    $results = $results->merge(
        User::search($query)->get()->map(fn($u) => [
            'type' => 'user',
            'data' => $u,
        ])
    );

    return response()->json($results->values());
}
