<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardPosition extends Model
{
    /** @use HasFactory<\Database\Factories\BoardPositionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
