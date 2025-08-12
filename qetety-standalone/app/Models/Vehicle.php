<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand', 'model', 'year', 'engine_type', 'vin',
        'plate_number', 'owner_id', 'is_active'
    ];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }

    public function compatibleParts()
    {
        return $this->belongsToMany(Part::class, 'vehicle_part_compatibility');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', 'like', "%{$brand}%");
    }

    public function scopeByYear($query, $yearFrom, $yearTo = null)
    {
        $query->where('year', '>=', $yearFrom);
        if ($yearTo) {
            $query->where('year', '<=', $yearTo);
        }
        return $query;
    }

    // Helpers
    public function getFullNameAttribute()
    {
        return "{$this->brand} {$this->model} {$this->year}";
    }

    public static function getBrands()
    {
        return [
            'toyota' => 'تويوتا',
            'honda' => 'هوندا', 
            'nissan' => 'نيسان',
            'hyundai' => 'هيونداي',
            'kia' => 'كيا',
            'mazda' => 'مازدا',
            'mitsubishi' => 'ميتسوبيشي',
            'suzuki' => 'سوزوكي',
            'bmw' => 'بي ام دبليو',
            'mercedes' => 'مرسيدس',
            'audi' => 'أودي',
            'volkswagen' => 'فولكس واجن',
            'peugeot' => 'بيجو',
            'renault' => 'رينو',
            'ford' => 'فورد',
            'chevrolet' => 'شيفروليه',
            'gmc' => 'جي ام سي',
            'jeep' => 'جيب'
        ];
    }
}
