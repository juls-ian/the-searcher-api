<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Archive extends Model
{
    /** @use HasFactory<\Database\Factories\ArchiveFactory> */
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'archivable_type',
        'archivable_id',
        'title',
        'slug',
        'data',
        'archived_at',
        'archiver_id'
    ];

    protected $casts = [
        'data' => 'array',
        'archived_at' => 'datetime'
    ];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'data' => $this->formattedDataByType(),
        ];
    }

    // Polymorphic relationship to other models 
    public function archivable()
    {
        return $this->morphTo();
    }

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archiver_id');
    }

    // Conditional data, transferred from resource to here 
    public function formattedDataByType(): array
    {
        // Normalize data into array no matter what 
        $data = is_array($this->data) ? $this->data : json_decode($this->data, true);

        switch ($this->archivable_type) {
            case 'article':
                return [
                    'category' => ArticleCategory::find($data['article_category_id'])?->only(['name']),
                    'writer' => User::find($data['writer_id'])?->only(['full_name']),
                    'body' => $data['body'],
                    'published_at' => $data['published_at'],
                    'cover_photo' => $data['cover_photo'],
                    'cover_artist' => User::find($data['cover_artist_id'])?->only(['full_name']),
                    'credit_type' => $data['credit_type'] ?? null
                ];

            case 'multimedia':
                $multimediaArtists = [];
                if (isset($data['multimedia_artists_id']) && is_array($data['multimedia_artists_id'])) {
                    $multimediaArtists = User::whereIn('id', $data['multimedia_artists_id'])
                        ->get()
                        ->map(fn($user) => $user->only(['id', 'full_name']))
                        ->toArray();
                }

                $files = [];
                if (isset($data['files'])) {
                    if (is_array($data['files'])) {
                        $files = $this->isAssoc($data['files']) ? [$data['files']] : $data['files'];
                    } else {
                        $decoded = json_decode($data['files'], true);
                        $files = json_last_error() === JSON_ERROR_NONE && is_array($decoded)
                            ? $decoded
                            : [$data['files']];
                    }
                }

                return [
                    'category' => $data['category'],
                    'caption' => $data['caption'],
                    'published_at' => $data['published_at'],
                    'files' => $files,
                    'multimedia_artists' => $multimediaArtists,
                    'thumbnail' => $data['thumbnail'],
                    'thumbnail_artist' => User::find($data['thumbnail_artist_id'])?->only(['full_name']),
                    'credit_type' => $data['credit_type'] ?? null
                ];

            case 'community-segment':
                $segment = $this->archivable()->with('segmentArticles')->first();
                return [
                    'writer' => User::find($data['writer_id'])?->only(['full_name']),
                    'series_type' => $data['series_type'],
                    'series_of' => $data['series_of'],
                    'published_at' => $data['published_at'],
                    'series_order' => $data['series_order'],
                    'body' => $segment?->segmentArticles?->body,
                    'segment_cover' => $data['segment_cover'],
                    'cover_artist' => User::find($data['cover_artist_id'])?->only(['full_name']),
                    'credit_type' => $data['credit_type'] ?? null
                ];

            case 'bulletin':
                return [
                    'writer' => User::find($data['writer_id'])?->only(['full_name']),
                    'category' => $data['category'],
                    'details' => $data['details'],
                    'published_at' => $data['published_at'],
                    'cover_photo' => $data['cover_photo'],
                    'cover_artist' => User::find($data['cover_artist_id'])?->only(['full_name']),
                ];

            case 'issue':
                return [
                    'description' => $data['description'],
                    'published_at' => $data['published_at'],
                    'editors' => $data['editors'],
                    'writers' => $data['writers'],
                    'photojournalists' => $data['photojournalists'],
                    'artists' => $data['artists'],
                    'layout_artists' => $data['layout_artists'],
                    'contributors' => $data['contributors'],
                    'file' => $data['file'],
                    'thumbnail' => $data['thumbnail']
                ];

            default:
                return $data;
        }
    }

    private function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
