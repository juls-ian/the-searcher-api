# Scrapped codes in the MultimediaObserver

## v.1: creating() & updating() - uses uniqid
  public function creating(Multimedia $multimedia)
    {

        $baseSlug = Str::slug($multimedia->title); # convert title into slug 
        $slugDate = now()->format('Y-m-d');
        $slug = "{$baseSlug}-{$slugDate}";

        // Check if slug exists in db 
        while (Multimedia::where('slug', $slug)->exists()) {
            $uniqueId = substr(uniqid(), 0, 8);
            $slug = "{$slug}-{$uniqueId}";
        }
        $multimedia->slug = $slug;
    }

    /**
     * Runs before a new multimedia is updated in database
     */
    public function updating(Multimedia $multimedia)
    {
        if ($multimedia->isDirty('title')) { # check of media is modified 
            $baseSlug = Str::slug($multimedia->title);
            $slugDate = now()->format('Y-m-d');
            $slug = "{$baseSlug}-{$slugDate}";

            // Check if any other multimedia has the slug 
            while (
                Multimedia::where('slug', $slug) # check if slug exists
                ->where('id', '!=', $multimedia->id) # ensure not to compare slug to itself
                ->exists()
            ) {
                $uniqueId = substr(uniqid(), 0, 8);
                $slug =  "{$slug}-{$uniqueId}";
            }
            $multimedia->slug = $slug; # final base value 
        }
    }
