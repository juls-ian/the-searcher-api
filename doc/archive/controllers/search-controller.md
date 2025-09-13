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

## searchArchives()
### 1.0: initial code
use App\Models\Archive;

public function searchArchives(Request $request)
{
    $query = $request->input('q', ''); // search keyword
    $year = $request->input('year');
    $sort = $request->input('sort', 'desc'); // 'asc' or 'desc'
    $from = $request->input('from'); // yyyy-mm-dd
    $to = $request->input('to');     // yyyy-mm-dd

    $search = Archive::search($query, function ($meilisearch, $query, $options) use ($year, $from, $to) {
        if ($year) {
            $options['filter'][] = "year = $year";
        }

        if ($from && $to) {
            $options['filter'][] = "published_at >= $from AND published_at <= $to";
        }

        return $meilisearch->search($query, $options);
    });

    // Apply sorting
    $search->orderBy('published_at', $sort);

    return $search->paginate(10);
}
### 1.1: other version
   public function archive(Request $request)
    {
        $query = $request->input('q', ''); // search keyword
        $year = $request->input('year');
        $month = $request->input('month');
        $sort = $request->input('sort', 'desc'); // 'asc' or 'desc'
        $from = $request->input('from'); // yyyy-mm-dd
        $to = $request->input('to');     // yyyy-mm-dd

        $search = Archive::search($query, function ($meilisearch, $query, $options) use ($year, $month, $from, $to) {
            if ($year) {
                $options['filter'][] = "year = $year";
            }

            if ($month) {
                $options['filter'][] = "month = \"$month\"";
            }

            if ($from && $to) {
                $options['filter'][] = "published_at >= $from AND published_at <= $to";
            }

            return $meilisearch->search($query, $options);
        });

        // Apply sorting
        $search->orderBy('published_at', $sort);

        return $search->paginate(10);
    }