<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'part_id', 'quantity', 'price', 'total',
        'part_name', 'part_sku', 'part_image', 'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    // Calculate total automatically
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateTotal();
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        if (isset($this->attributes['quantity']) && isset($this->attributes['price'])) {
            $this->attributes['total'] = $this->attributes['quantity'] * $this->attributes['price'];
        }
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ريال';
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . ' ريال';
    }
}
