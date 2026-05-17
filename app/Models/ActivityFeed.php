<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityFeed extends Model
{
    protected $fillable = [
        'user_id', 'activity_type', 'description', 'icon', 'color', 'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $type, string $description, ?array $meta = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'activity_type' => $type,
            'description' => $description,
            'meta_data' => $meta,
        ]);
    }
}
