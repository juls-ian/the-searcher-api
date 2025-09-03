# Unused codes in the Community Segment model


## initial code
class CommunitySegment extends Model
{
    /** @use HasFactory<\Database\Factories\CommunitySegmentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'segment_type',
        'writer_id',
        'published_at',
        'series_of',
        'series_order',
        'segment_cover',
        'cover_artist_id',
        'cover_caption'
    ];


    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function series()
    {
        return $this->hasMany(self::class, 'series_of');
    }

    public function coverArtist()
    {
        return $this->belongsTo(User::class, 'cover_artist');
    }

    // Polymorphism relationship
    public function segmentable()
    {
        return $this->morphTo();
    }
}

## toSearchable()
### 1.0: using optional 
public function toSearchableArray()
{
    return [
        'title' => $this->title,
        'writer' => optional($this->writer)->full_name,
        'cover_artist' => optional($this->coverArtist)->full_name,
        'body' => optional($this->segmentArticles)->body,
        'question' => optional($this->segmentPolls)->question,
    ];
}
### 1.1: using null-safe operator
public function toSearchableArray()
{
    return [
        'title' => $this->title,
        'writer' => $this->writer?->full_name,
        'cover_artist' => $this->coverArtist?->full_name,
        'body' => $this->segmentArticles?->body,
        'question' => $this->segmentPolls?->question,
    ];
}
### 1.2: using switch 
public function toSearchableArray(): array
{
    $data = [
        'title'        => $this->title,
        'writer'       => $this->writer?->full_name,
        'cover_artist' => $this->coverArtist?->full_name,
    ];

    switch ($this->segment_type) {
        case 'article':
            $data['body'] = $this->segmentArticles?->body;
            break;

        case 'poll':
            $data['question'] = $this->segmentPolls?->question;
            break;
    }

    return $data;
}
