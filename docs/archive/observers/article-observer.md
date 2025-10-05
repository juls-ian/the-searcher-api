# Scrapped codes in the ArticleObserver

## v.1: creating() & updating() - append +1 
    public function creating(Article $article)
    {
        $slug = Str::slug($article->title); # convert into slug
        $originalSlug = $slug; # store orig slug
        $count = 1;

        // Check if slug already exists in db 
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++; # if it exist, appends the number 
        }

        $article->slug = $slug; # final value 
    }

    public function updating(Article $article)
    {
        if ($article->isDirty('title')) { # Check if title has been modified
            $slug = Str::slug($article->title);
            $originalSlug = $slug;
            $count = 1;

            // Check if any OTHER article has this slug
            while (
                Article::where('slug', $slug) # check if slug exists
                    ->where('id', '!=', $article->id) # ensure not to compare slug to itself
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            $article->slug = $slug; # final value 
        }
    }




## v.2: creating() & updating - adds date 
public function creating(Multimedia $multimedia) 
{
    $baseSlug = Str::slug($multimedia->title);
    
    // Try base slug first
    if (!Multimedia::where('slug', $baseSlug)->exists()) {
        $multimedia->slug = $baseSlug;
        return;
    }
    
    // Add date if base slug exists
    $multimedia->slug = $baseSlug . '-' . now()->format('Y-m-d');
}

public function updating(Multimedia $multimedia)
{
    if ($multimedia->isDirty('title')) {
        $baseSlug = Str::slug($multimedia->title);
        
        // Try base slug first
        if (!Multimedia::where('slug', $baseSlug)->where('id', '!=', $multimedia->id)->exists()) {
            $multimedia->slug = $baseSlug;
            return;
        }
        
        // Add date if base slug exists
        $multimedia->slug = $baseSlug . '-' . now()->format('Y-m-d');
}


## v.3: creating() & updating() - appends date, more function
public function creating(Multimedia $multimedia) 
{
    $multimedia->slug = $this->generateNewsStyleSlug($multimedia->title);
} 

public function updating(Multimedia $multimedia)
{
    if($multimedia->isDirty('title')) {
        $multimedia->slug = $this->generateNewsStyleSlug($multimedia->title, $multimedia->id);
    }
}

    public function generateStandardizedSlug($title, $excludedId = null)
    {

        $baseSlug = Str::slug($title);

        // Try the base slug first 
        if (!$this->slugExists($baseSlug, $excludedId)) {
            return $baseSlug;
        }

        // Format 1: article-title-2025-08-28
        $dateSlug = $baseSlug . '-' . now('Y-m-d');
        if (!$this->slugExists($dateSlug, $excludedId)) {
            return $dateSlug;
        }

        // Format 2
        $dateTimeSlug = $baseSlug . '-' . now()->format('Y-m-d-H-i');
        if (!$this->slugExists($dateTimeSlug, $excludeId)) {
        return $dateTimeSlug;
    }
    }

    public function slugExists($slug, $excludedId = null)
    {
        $query = Multimedia::where('slug', $slug); # slug col matches given slug 

        # if an excludeId is given; exclude that record from the query 
        if ($excludedId) {
            $query->where('id', '!=', $excludedId);
        }

        return $query->exists(); # returns boolean
    }