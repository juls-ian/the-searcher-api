# Scrapped codes in Calendar model 

## accessor 
### 1.0: initial version to update status dynamically | problematic
    public function getStatusAttribute()
    {

        // Non allday events 
        $now = now();

        if ($this->start_at > $now) {
            return 'upcoming';
        } elseif ($this->ends_at < $now) {
            return 'concluded';
            # "<=" for time precision milliseconds 
        } elseif ($this->start_at <= $now && (!$this->ends_at || $this->ends_at >= $now)) {
            return 'happening';
        }
        return 'upcoming'; # fall back 
    }
### 1.1: is_allday is being set to false (with issue)
    public function getStatusAttribute()
    {
        // Allday events 
        if ($this->is_allday) {
            $today = now()->startOfDay();
            $startDate = $this->start_at->startOfDay();
            $endDate = $this->ends_at ? $this->ends_at->startOfDay() : null;

            if ($today->lt($startDate)) { # $today < $startDate 
                return 'upcoming';
            }
            if ($endDate && $today->gt($endDate)) {  # $today > $endDate 
                return 'concluded';
            }
            return 'happening'; # fallback 
        }

        // Non allday events 
        $now = now();

        if ($this->start_at > $now) {
            return 'upcoming';
        }
        if ($this->ends_at && $this->ends_at < $now) {
            return 'concluded';
        }
        return 'happening'; # fall back 
    }
### 1.2: debug
    public function getStatusAttribute()
    {
        // Allday events 
        if ($this->is_allday) {
            $today = now()->format('Y-m-d');
            $startDate = $this->start_at->format('Y-m-d');
            $endDate = $this->ends_at ? $this->ends_at->format('Y-m-d') : null;

            if ($today < $startDate) {
                return 'upcoming';
            }
            if ($endDate && $today > $endDate) {  # $today > $endDate 
                return 'concluded';
            }
            return 'happening'; # fallback 
        }

        // Non allday events 
        $now = now();
        Log::info('Status Debug', [
            'now' => $now->toISOString(),
            'now_timestamp' => $now->timestamp,
            'start_at' => $this->start_at->toISOString(),
            'start_timestamp' => $this->start_at->timestamp,
            'ends_at' => $this->ends_at ? $this->ends_at->toISOString() : null,
            'end_timestamp' => $this->ends_at ? $this->ends_at->timestamp : null,
            'start_comparison' => $this->start_at > $now ? 'start > now (upcoming)' : 'start <= now',
            'end_comparison' => $this->ends_at && $this->ends_at < $now ? 'end < now (concluded)' : 'end >= now',
            'timezone_now' => $now->timezone->getName(),
            'timezone_start' => $this->start_at->timezone->getName(),
        ]);

        if ($this->start_at > $now) {
            return 'upcoming';
        }
        if ($this->ends_at && $this->ends_at < $now) {
            return 'concluded';
        }
        return 'happening'; # fall back 
    }