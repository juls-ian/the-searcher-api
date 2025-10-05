# Scrapped codes in the SearchService

## searchArchives()
### 1.0: full date range filter
```php
    public function searchArchives(array $params)
    {
        $query = $params['q'] ?? '*';
        $year  = $params['year'] ?? null;
        $month = $params['month'] ?? null;
        $sort  = $params['sort'] ?? 'desc';
        $from  = $params['from'] ?? null;
        $to    = $params['to'] ?? null;

        $search = Archive::search($query, function ($meilisearch, $query, $options) use ($year, $month, $from, $to) {

            $filters = [];

            if ($year) {
                $filters[] = "year = $year";
            }

            if ($month) {
                $filters[] = "month = \"$month\""; # needs quotes since it's a string in Meilisearch
            }

            if ($from && $to) {
                $filters[] = "archived_at >= $from AND archived_at <= $to";
            }

            if ($filters) {
                $options['filter'] = implode(' AND ', $filters);
            }

            return $meilisearch->search($query, $options);
        });

        return $search->orderBy('archived_at', $sort)->paginate(10);
    }
```
### 1.1: with additional conditions 
```php
    public function searchArchives(array $params)
    {
        $search = Archive::search($query, function ($meilisearch, $query, $options) use ($year, $month, $from, $to) {

            $filters = [];

            if ($year) {
                $filters[] = "year = $year";
            }

            if ($month) {
                $filters[] = "month = \"$month\""; # needs quotes since it's a string in Meilisearch
            }

            // Case 1: Year + Month 
            if ($year && $month) {
                $start = sprintf('%04d-%02d-01', $year, $month); # $year = 2025, $month = 9 → "2025-09-01"
                $end = date('Y-m-d', strtotime("$start +1 month")); # $start = "2025-09-01" → $end = "2025-10-01"
                # builds the filter 
                $filters[] = "archived_at >= $start AND archived_at < $end"; # archived_at >= 2025-09-01 AND archived_at < 2025-10-01

            } // Case 2: Year only  
            elseif ($year) {
                $start = "$year-01-01"; # January 1st of given year
                $end = date('Y-m-d', strtotime("$start +1 $year")); # add 1 year to start 
                # capture all record for the year 
                $filters[] = "archived_at >= $start AND archived_at < $end";

            } // Case 3: Custom date range  
            elseif ($from && $to) {
                $filters[] = "archived_at >= $from AND archived_at <= $to";
            }

            if ($filters) {
                $options['filter'] = implode(' AND ', $filters);
            }

            return $meilisearch->search($query, $options);
        });

        return $search->orderBy('archived_at', $sort)->paginate(10);
    }
```
### 1.2: year + month not working
```php
    public function searchArchives(array $params)
    {
        $search = Archive::search($query, function ($meilisearch, $query, $options) use ($year, $month, $from, $to) {

            $filters = [];

            // Case 1: Year only 
            if ($year) {
                $filters[] = "year = $year";
            }

            // Case 2: Month only 
            if ($month) {
                $filters[] = "month = \"$month\""; # needs quotes since it's a string in Meilisearch
            }

            // Case 3: Year + Month - specific month in a year
            if ($year && $month) {

                // Convert month name to number if necessary
                if (!is_numeric($month)) {
                    $month = date('m', strtotime($month));
                } else {
                    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                }

                $start = sprintf('%04d-%02d-01', $year, $month); # $year = 2025, $month = 9 → "2025-09-01"
                $end = date('Y-m-d', strtotime("$start +1 month")); # $start = "2025-09-01" → $end = "2025-10-01"
                # builds the filter 
                $filters[] = "archived_at >= $start AND archived_at < $end"; # archived_at >= 2025-09-01 AND archived_at < 2025-10-01
            } // Case 4: Custom date range  
            elseif ($from && $to) {
                # interpret $from & $to as years if both are 4 digits 
                if (strlen($from) === 4 && strlen($to) === 4) {
                    $start = "$from-01-01";
                    # converts $to (string) to int when +1 = 2025 + 1 = 2026
                    $end = ($to + 1) . "-01-01"; # exclusive upper bound trick (< 2026-01-01)
                    $filters[] = "archived_at >= $start AND archived_at < $end";
                } else {
                    # fallback
                    $filters[] = "archived_at >= $from AND archived_at <= $to";
                }
            }
            /**
             * takes ["year = 2024", "month = \"05\""] and implode turn it into a single string 
             * "year = 2024 AND month = \"05\""
             */
            if ($filters) {
                $options['filter'] = implode(' AND ', $filters);
            }

            return $meilisearch->search($query, $options);
        })

        return $search->orderBy('archived_at', $sort)->paginate(10);
    }
    ```