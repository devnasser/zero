<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Part extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'name_en', 'name_ar', 'description', 'description_ar',
        'sku', 'original_part_number', 'brand', 'model', 'category_id',
        'price', 'cost_price', 'stock_quantity', 'min_stock', 'max_stock',
        'weight', 'dimensions', 'material', 'origin_country',
        'warranty_months', 'is_original', 'is_compatible', 'is_universal',
        'condition', 'seller_id', 'is_featured', 'is_active', 'rating',
        'views_count', 'sales_count', 'meta_keywords', 'meta_description',
        'saber_certification', 'government_approved'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'json',
        'warranty_months' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'is_original' => 'boolean',
        'is_compatible' => 'boolean',
        'is_universal' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'saber_certification' => 'boolean',
        'government_approved' => 'boolean',
        'meta_keywords' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function compatibleVehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_part_compatibility')
                    ->withPivot('year_from', 'year_to', 'engine_type', 'notes')
                    ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(PartImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(PartReview::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function alternatives()
    {
        return $this->belongsToMany(Part::class, 'part_alternatives', 'part_id', 'alternative_id');
    }

    public function relatedParts()
    {
        return $this->belongsToMany(Part::class, 'related_parts', 'part_id', 'related_part_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeOriginal($query)
    {
        return $query->where('is_original', true);
    }

    public function scopeCompatible($query)
    {
        return $query->where('is_compatible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', 'like', "%{$brand}%");
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('name_en', 'like', "%{$term}%")
              ->orWhere('name_ar', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('original_part_number', 'like', "%{$term}%")
              ->orWhere('brand', 'like', "%{$term}%")
              ->orWhere('model', 'like', "%{$term}%");
        });
    }

    public function scopeHighRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeBestSelling($query)
    {
        return $query->orderBy('sales_count', 'desc');
    }

    public function scopeMostViewed($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock');
    }

    // Accessors & Mutators
    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . $this->brand . ' ' . $this->model;
    }

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name) : $this->name;
    }

    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?: $this->description) : $this->description;
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ريال';
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->cost_price && $this->price > $this->cost_price) {
            return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
        }
        return 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity == 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->min_stock) {
            return 'low_stock';
        } elseif ($this->stock_quantity >= $this->max_stock) {
            return 'overstock';
        }
        return 'in_stock';
    }

    public function getMainImageAttribute()
    {
        $mainImage = $this->images()->where('is_main', true)->first();
        return $mainImage ? $mainImage->image_url : '/images/parts/default.jpg';
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementSales($quantity = 1)
    {
        $this->increment('sales_count', $quantity);
    }

    public function updateStock($quantity, $operation = 'subtract')
    {
        if ($operation === 'subtract') {
            $this->decrement('stock_quantity', $quantity);
        } else {
            $this->increment('stock_quantity', $quantity);
        }
    }

    public function calculateCompatibilityScore(Vehicle $vehicle)
    {
        $score = 0;
        
        // Brand match
        if (stripos($this->brand, $vehicle->brand) !== false) {
            $score += 30;
        }
        
        // Model match
        if (stripos($this->model, $vehicle->model) !== false) {
            $score += 25;
        }
        
        // Year compatibility
        $compatible = $this->compatibleVehicles()
                          ->where('vehicle_id', $vehicle->id)
                          ->first();
        if ($compatible) {
            $score += 40;
        }
        
        // Universal parts
        if ($this->is_universal) {
            $score += 20;
        }
        
        return min(100, $score);
    }

    public function getRecommendations($limit = 5)
    {
        return self::where('category_id', $this->category_id)
                  ->where('id', '!=', $this->id)
                  ->where('is_active', true)
                  ->where('stock_quantity', '>', 0)
                  ->orderBy('sales_count', 'desc')
                  ->orderBy('rating', 'desc')
                  ->limit($limit)
                  ->get();
    }

    // Static methods
    public static function getConditions()
    {
        return [
            'new' => 'جديد',
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'مقبول',
            'poor' => 'ضعيف',
            'refurbished' => 'مجدد',
            'used' => 'مستعمل'
        ];
    }

    public static function getMaterials()
    {
        return [
            'steel' => 'فولاذ',
            'aluminum' => 'ألومنيوم',
            'plastic' => 'بلاستيك',
            'rubber' => 'مطاط',
            'ceramic' => 'سيراميك',
            'composite' => 'مركب',
            'other' => 'أخرى'
        ];
    }

    public static function getPopularBrands()
    {
        return self::select('brand')
                  ->groupBy('brand')
                  ->orderByRaw('COUNT(*) DESC')
                  ->limit(20)
                  ->pluck('brand')
                  ->toArray();
    }
}
