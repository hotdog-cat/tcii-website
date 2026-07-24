<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ContentItem extends Model
{
    public const TYPES = [
        'facility' => 'Facilities',
        'equipment' => 'Equipment',
        'product' => 'Products',
        'project' => 'Projects',
        'team' => 'Team',
        'registry' => 'Business Registry',
    ];

    protected $fillable = ['type', 'title', 'subtitle', 'description', 'image_path', 'sort_order', 'is_published'];
    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'sort_order' => 'integer'];
    }
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        return str_starts_with($this->image_path, 'images/')
            ? asset($this->image_path)
            : Storage::url($this->image_path);
    }
}
