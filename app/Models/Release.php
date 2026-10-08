<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Release extends Model
{
    protected $fillable = [
        'version',
        'title',
        'description',
        'published_at',
        'active',
        'features'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('seen_at')
            ->withTimestamps();
    }
}
