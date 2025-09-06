<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;

class Calendar extends Model
{
    /** @use HasFactory<\Database\Factories\CalendarFactory> */
    use HasFactory, Searchable;

    protected $fillable = [
        'title',
        'start_at',
        'ends_at',
        'is_allday',
        'venue',
        'details',
        'is_public',
        'event_type'
    ];

    protected $casts = [
        'start_at' => 'datetime:Y-m-d H:i:s',
        'ends_at'  => 'datetime:Y-m-d H:i:s',
        'is_allday' => 'boolean',
        'is_public' => 'boolean',
    ];

    // Default values 
    protected $attributes = [
        'is_allday' => true,
        'is_public' => true
    ];

    protected $appends = ['status']; // adds 'status' to JSON responses

    // For the search engine
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'venue' => $this->venue,
            'details' => $this->details,
            'event_type' => $this->event_type,
        ];
    }

    // Dynamic status indicator - Model accessor
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
}
