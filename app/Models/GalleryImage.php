<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'path',
        'name',
        'description',
        'is_public',
        'share_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function visitorLogs()
    {
        return $this->hasMany(VisitorLog::class);
    }
}
