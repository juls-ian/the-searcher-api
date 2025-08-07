<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditorialBoard extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'term',
        'is_current'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
