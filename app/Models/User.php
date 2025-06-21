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

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword, SoftDeletes;

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
        'term',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships to Article
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Article, User>
     */
    public function writtenArticles()
    {
        return $this->hasMany(Article::class, 'writer_id');
    }

    public function coverContributions()
    {
        return $this->hasMany(Article::class, 'cover_artist_id');
    }

    public function thumbnailContributions()
    {
        return $this->hasMany(Article::class, 'thumbnail_artist_id');
    }

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

        $fNameInitial = strtoupper(substr($lastName, 0, 1));

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