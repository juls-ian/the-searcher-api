<?php

namespace App\Http\Resources;

use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = json_decode($this->data, true);
        # defaults 
        return [
            'id' => $this->id,
            'archive_type' => $this->archivable_type,
            'archived_id' => $this->archivable_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'data' => $this->formDataByType($data),
            'archived_at' => $this->archived_at,
            'archiver_id' => $this->archiver->full_name ?? null,
        ];
    }

    public function formDataByType(array $data)
    {
        switch ($this->archivable_type) {
            case 'article':
                return [
                    /**
                     * "?->" is a null-safe operator - If the left side is null, 
                     * the entire expression returns null instead of throwing an error; 
                     * ensures no error occurs if the find() result is null
                     */
                    'category' => ArticleCategory::find($data['article_category_id'])?->only(['name']),
                    'writer' => User::find($data['writer_id'])?->only(['full_name']),
                    'body' => $data['body'],
                    'published_at' => $data['published_at'],
                    'cover_photo' => $data['cover_photo'],
                    'cover_artist' => User::find($data['cover_artist_id'])?->only(['full_name']),
                    'credit_type' => $data['credit_type']
                ];

            case 'multimedia':
                return [
                    'category' => $data['category'],
                    'caption' => $data['caption'],
                    'published_at' => $data['published_at'],
                    'files'  => $data['files'],
                    'multimedia_artists' => User::find($data['multimedia_artists_id'])?->only(['full_name']),
                    'thumbnail' => $data['thumbnail'],
                    'thumbnail_artist' => User::find($data['thumbnail_artist_id'])?->only(['full_name']),
                    'credit_type' => $data['credit_type']
                ];

            case 'community-segment':
                return [
                    'writer' => User::find($data['writer_id'])?->only(['full_name']),
                    'series_type' => $data['series_type'],
                    'series_of' => $data['series_of'],
                    'published_at' => $data['published_at'],
                    'series_order' => $data['series_order'],
                    'body' => $data['body'],
                    'segment_cover' => $data['segment_cover'],
                    'cover_artist' => User::find($data['cover_artist_id'])?->only(['full_name']),
                    'credit_type' => $data['credit_type']
                ];

            case 'bulletin':
                return [
                    'writer' => User::find($data['writer_id'])?->only(['full_name']),
                    'category' => $data['category'],
                    'details' => $data['details'],
                    'published_at' => $data['published_at'],
                    'cover_photo' => $data['cover_photo'],
                    'cover_artist' => $data['cover_artist']
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
                    'issue_file' => $data['issue_file'],
                    'thumbnail' => $data['thumbnail']
                ];
        }
    }
}
