<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            // Use whenLoaded to ensure it's only included if eager loaded
            // 'articles' => ArticleResource::collection($this->whenLoaded('articles'))
        ];
    }
}