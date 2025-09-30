<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword, SoftDeletes, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'pen_name',
        'year_level',
        'course',
        'phone',
        'board_position',
        'role',
        'status',
        'joined_at',
        'left_at',
        'profile_pic'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function toSearchableArray()
    {
        return [
            'pen_name' => $this->pen_name,
            'staff' => $this->full_name
        ];
    }

    /**
     * Relationship to EditorialBoard 
     */
    public function editorialBoards()
    {
        return $this->hasMany(EditorialBoard::class);
    }

    public function currentEditorialBoard()
    {
        return $this->hasOne(EditorialBoard::class)->where('is_current', true);
    }

    public function currentTerm()
    {
        if (!$this->relationLoaded('currentEditorialBoard')) {
            $this->load('currentEditorialBoard');
        }
        return $this->currentEditorialBoard?->term;
    }

    // Alternatives
    public function getCurrentTermAttribute()
    {
        return $this->editorialBoards()->latest()->value('term');
    }

    public function getAllTerms()
    {
        return $this->editorialBoards->pluck('term')->toArray();
    }

    public function getAllTermsCollection()
    {
        return $this->editorialBoards->pluck('term');
    }

    /**
     * Relationships to Article
     */
    public function writtenArticles()
    {
        return $this->hasMany(Article::class, 'writer_id');
    }

    public function articleCoverContributions()
    {
        return $this->hasMany(Article::class, 'cover_artist_id');
    }

    public function articleThumbnailContributions()
    {
        return $this->hasMany(Article::class, 'thumbnail_artist_id');
    }

    public function publishedArticles()
    {
        return $this->hasMany(Article::class, 'publisher_id');
    }

    /**
     * Relationships to CommunitySegment
     */
    public function writtenSegments()
    {
        return $this->hasMany(CommunitySegment::class, 'writer_id');
    }

    public function segmentCoverContributions()
    {
        return $this->hasMany(CommunitySegment::class, 'cover_artist_id');
    }

    public function publishedSegment()
    {
        return $this->hasMany(CommunitySegment::class, 'publisher_id');
    }

    /**
     * Relationships to Multimedia 
     */
    public function multimediaContributions()
    {
        return $this->hasMany(Multimedia::class, 'multimedia_artists_id');
    }

    public function multimediaThumbnailContributions()
    {
        return $this->hasMany(Multimedia::class, 'thumbnail_artist_id');
    }

    public function publishedMultimedia()
    {
        return $this->hasMany(Multimedia::class, 'publisher_id');
    }

    /**
     * Relationships to Bulletin 
     */
    public function writtenBulletin()
    {
        return $this->hasMany(Bulletin::class, 'writer_id');
    }

    public function bulletinCoverContributions()
    {
        return $this->hasMany(Bulletin::class, 'cover_artist_id');
    }

    public function publishedBulletin()
    {
        return $this->hasMany(Bulletin::class, 'publisher_id');
    }

    /**
     * Relationship to Issues 
     */
    public function publishedIssue()
    {
        return $this->hasMany(Issue::class, 'publisher_id');
    }

    /**
     * Relationship to Archive
     */
    public function archivedEntries()
    {
        return $this->hasMany(Archive::class, 'archiver_id');
    }

    // ---------------------------------------------


    /**
     * Register ResetPassword notification in the model
     * 
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Getting all contributions 
     * @return Article
     */
    public function allContributions()
    {
        return Article::where('writer_id', $this->id)
            ->orWhere('cover_artist_id', $this->id)
            ->orWhere('thumbnail_artist_id', $this->id);
    }


    /**
     * Assemble the first and last name
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Generating Staff Id 
     * Format: [first 3 letters of surname]-[joining year]-[fName 1st letter]-[4 digit increment]
     */
    public static function generateStaffId($firstName, $lastName, $joiningDate = null)
    {
        // current year if joining date not provided 
        $year = $joiningDate ? Carbon::parse($joiningDate)->year : Carbon::now()->year;

        // get first 3 letters of last name
        $lNamePrefix = strtoupper(substr($lastName, 0, 3));

        $fNameInitial = strtoupper(substr($firstName, 0, 1));

        // find highest existing increment this year and name combination
        $pattern = $lNamePrefix . '-' . $year . '-' . $fNameInitial . '-' . '%';

        // for the increment sequence
        $latestUser = self::where('staff_id', 'LIKE', $pattern)
            ->orderBy('staff_id', 'desc')
            ->first();

        $increment = 1;

        if ($latestUser) {
            // extracting the last 4 digits and increment 
            $lastStaffId = $latestUser->staff_id;
            $lastIncrement = (int) substr($lastStaffId, -4);
            $increment = $lastIncrement + 1;
        }

        /**
         * Formatting increment as 4-digit number with leading zeros
         * $increment = 1 
         * result: 0001
         */
        $incrementFormat = str_pad($increment, 4, '0', STR_PAD_LEFT);

        // final id combination
        return $lNamePrefix . '-' . $year . '-' . $fNameInitial . '-' . $incrementFormat;
    }


    /**
     * Auto-generate the id with Boot method when creating user 
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->staff_id)) {
                $user->staff_id = self::generateStaffId(
                    $user->first_name,
                    $user->last_name,
                    $user->joining_date ?? now()
                );
            }
        });
    }
}
