<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'gallery_image_id',
        'category_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'camera_image_path',
        'camera_image_base64',
        'latitude',
        'longitude',
        'address',
        'location_accuracy'
    ];

    public function image()
    {
        return $this->belongsTo(GalleryImage::class, 'gallery_image_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
