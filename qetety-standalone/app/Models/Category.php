<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_ar', 'name_en', 'slug', 'description', 'description_ar',
        'parent_id', 'level', 'sort_order', 'icon', 'image', 'meta_title',
        'meta_description', 'meta_keywords', 'is_active', 'is_featured',
        'commission_rate', 'min_commission', 'category_type', 'attributes',
        'seo_url', 'canonical_url', 'schema_markup'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'commission_rate' => 'decimal:2',
        'min_commission' => 'decimal:2',
        'level' => 'integer',
        'sort_order' => 'integer',
        'attributes' => 'json',
        'meta_keywords' => 'array',
        'schema_markup' => 'json'
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }

    public function allParts()
    {
        return $this->hasManyThrough(Part::class, Category::class, 'parent_id', 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Methods
    public function getFullNameAttribute()
    {
        $names = collect([$this->name]);
        $parent = $this->parent;
        
        while ($parent) {
            $names->prepend($parent->name);
            $parent = $parent->parent;
        }
        
        return $names->join(' > ');
    }

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name) : $this->name;
    }

    public static function getMainCategories()
    {
        return [
            'engine' => 'محرك',
            'transmission' => 'ناقل الحركة',
            'brakes' => 'المكابح',
            'suspension' => 'التعليق',
            'electrical' => 'كهربائي',
            'body' => 'الهيكل',
            'interior' => 'الداخلية',
            'exterior' => 'الخارجية',
            'wheels_tires' => 'إطارات وجنوط',
            'fluids' => 'سوائل',
            'filters' => 'فلاتر',
            'cooling' => 'التبريد',
            'fuel' => 'الوقود',
            'exhaust' => 'العادم',
            'lighting' => 'الإضاءة'
        ];
    }
}
