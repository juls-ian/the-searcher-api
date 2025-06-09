<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleCategory>
 */
class ArticleCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'parent_id' => null, // Default to no parent
        ];
    }

    /**
     * Parent Categories 
     */

    // News parent category 
    public function news()
    {
        return $this->state([
            'name' => 'News',
            'slug' => 'news',
            'parent_id' => null
        ]);
    }

    // Feature parent category 
    public function feature()
    {
        return $this->state([
            'name' => 'Feature',
            'slug' => 'feature',
            'parent_id' => null
        ]);
    }

    // Opinion parent category 
    public function opinion()
    {
        return $this->state([
            'name' => 'Opinion',
            'slug' => 'opinion',
            'parent_id' => null
        ]);
    }

    // Literary parent category
    public function literary()
    {
        return $this->state([
            'name' => 'Literary',
            'slug' => 'literary ',
            'parent_id' => null
        ]);
    }

    // Sports parent category 
    public function sports()
    {
        return $this->state([
            'name' => 'Sports',
            'slug' => 'sports',
            'parent_id' => null
        ]);
    }

    /**
     * Subcategories
     */

    // News subcategories
    public function newsCampus($parentId): static
    {
        return $this->state([
            'name' => 'Campus',
            'slug' => 'campus',
            'parent_id' => $parentId,
        ]);
    }

    public function newsRegion($parentId): static
    {
        return $this->state([
            'name' => 'Region',
            'slug' => 'region',
            'parent_id' => $parentId,
        ]);
    }

    public function newsNation($parentId): static
    {
        return $this->state([
            'name' => 'Nation',
            'slug' => 'nation',
            'parent_id' => $parentId,
        ]);
    }

    public function newsWorld($parentId): static
    {
        return $this->state([
            'name' => 'World',
            'slug' => 'world',
            'parent_id' => $parentId,
        ]);
    }

    // Opinion subcategories

    public function opinionColumn($parentId): static
    {
        return $this->state([
            'name' => 'Column',
            'slug' => 'column',
            'parent_id' => $parentId,
        ]);
    }

    public function opinionEditorial($parentId): static
    {
        return $this->state([
            'name' => 'Editorial',
            'slug' => 'editorial',
            'parent_id' => $parentId,
        ]);
    }

    // Feature subcategories
    public function featureArtsCulture($parentId): static
    {
        return $this->state([
            'name' => 'Arts & Culture',
            'slug' => 'arts-culture',
            'parent_id' => $parentId,
        ]);
    }

    public function featureSpotlight($parentId): static
    {
        return $this->state([
            'name' => 'Spotlight',
            'slug' => 'spotlight',
            'parent_id' => $parentId,
        ]);
    }

    public function featurePulse($parentId): static
    {
        return $this->state([
            'name' => 'Pulse',
            'slug' => 'pulse',
            'parent_id' => $parentId,
        ]);
    }

    public function featureCircle($parentId): static
    {
        return $this->state([
            'name' => 'Circle',
            'slug' => 'circle',
            'parent_id' => $parentId,
        ]);
    }

    public function featureNeighborhood($parentId): static
    {
        return $this->state([
            'name' => 'Neighborhood',
            'slug' => 'neighborhood',
            'parent_id' => $parentId,
        ]);
    }

    // Literary subcategories
    public function literaryFiction($parentId): static
    {
        return $this->state([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'parent_id' => $parentId,
        ]);
    }

    public function literaryNonFiction($parentId): static
    {
        return $this->state([
            'name' => 'Non-Fiction',
            'slug' => 'non-fiction',
            'parent_id' => $parentId,
        ]);
    }

    public function literaryPoetry($parentId): static
    {
        return $this->state([
            'name' => 'Poetry',
            'slug' => 'poetry',
            'parent_id' => $parentId,
        ]);
    }

    // Sports subcategories
    public function sportsBasketball($parentId): static
    {
        return $this->state([
            'name' => 'Basketball',
            'slug' => 'basketball',
            'parent_id' => $parentId,
        ]);
    }

    public function sportsVolleyball($parentId): static
    {
        return $this->state([
            'name' => 'Volleyball',
            'slug' => 'volleyball',
            'parent_id' => $parentId,
        ]);
    }

    public function sportsEsports($parentId): static
    {
        return $this->state([
            'name' => 'Esports',
            'slug' => 'esports',
            'parent_id' => $parentId,
        ]);
    }
    public function sportsLarongPinoy($parentId): static
    {
        return $this->state([
            'name' => 'Larong Pinoy',
            'slug' => 'larong-pinoy',
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Complete category structure
     */
    public function createCompleteStructure(): static
    {
        // Create parent categories
        $news = ArticleCategory::factory()->news()->create();
        $feature = ArticleCategory::factory()->feature()->create();
        $opinion = ArticleCategory::factory()->opinion()->create();
        $sports = ArticleCategory::factory()->sports()->create();
        $literary = ArticleCategory::factory()->literary()->create();

        // Create News subcategories
        ArticleCategory::factory()->newsCampus($news->id)->create();
        ArticleCategory::factory()->newsRegion($news->id)->create();
        ArticleCategory::factory()->newsNation($news->id)->create();
        ArticleCategory::factory()->newsWorld($news->id)->create();

        // Create Opinion subcategories
        ArticleCategory::factory()->opinionColumn($opinion->id)->create();
        ArticleCategory::factory()->opinionEditorial($opinion->id)->create();

        // Create Feature subcategories
        ArticleCategory::factory()->featureArtsCulture($feature->id)->create();
        ArticleCategory::factory()->featureSpotlight($feature->id)->create();
        ArticleCategory::factory()->featurePulse($feature->id)->create();
        ArticleCategory::factory()->featureCircle($feature->id)->create();
        ArticleCategory::factory()->featureNeighborhood($feature->id)->create();

        // Create Literary subcategories
        ArticleCategory::factory()->literaryFiction($literary->id)->create();
        ArticleCategory::factory()->literaryNonFiction($literary->id)->create();
        ArticleCategory::factory()->literaryPoetry($literary->id)->create();

        // Create Sports subcategories
        ArticleCategory::factory()->sportsBasketball($sports->id)->create();
        ArticleCategory::factory()->sportsVolleyball($sports->id)->create();
        ArticleCategory::factory()->sportsEsports($sports->id)->create();
        ArticleCategory::factory()->sportsLarongPinoy($sports->id)->create();

        return $this;
    }
}