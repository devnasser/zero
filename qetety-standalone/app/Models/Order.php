<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'status', 'payment_status', 'payment_method',
        'subtotal', 'tax_amount', 'shipping_cost', 'discount_amount', 'total_amount',
        'currency', 'billing_address', 'shipping_address', 'customer_notes',
        'admin_notes', 'tracking_number', 'shipped_at', 'delivered_at',
        'estimated_delivery', 'urgency_level', 'workshop_id', 'vehicle_id',
        'payment_reference', 'invoice_number', 'tax_invoice_number'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'json',
        'shipping_address' => 'json',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'datetime',
        'urgency_level' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function reviews()
    {
        return $this->hasMany(OrderReview::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeUrgent($query)
    {
        return $query->where('urgency_level', '>=', 3);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    // Methods
    public function generateOrderNumber()
    {
        $prefix = 'QT' . date('y');
        $lastOrder = self::where('order_number', 'like', $prefix . '%')->latest()->first();
        $number = $lastOrder ? intval(substr($lastOrder->order_number, -6)) + 1 : 1;
        $this->order_number = $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $this->tax_amount = $this->subtotal * 0.15; // VAT 15%
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount;
    }

    public function updateStatus($newStatus, $notes = null)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();

        // Log status change
        $this->statusHistory()->create([
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $notes,
            'changed_by' => auth()->id(),
            'changed_at' => now()
        ]);

        // Trigger notifications
        $this->notifyStatusChange($newStatus);
    }

    public function canCancel()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canReturn()
    {
        return $this->status === 'delivered' && 
               $this->delivered_at->diffInDays(now()) <= 30;
    }

    public function getEstimatedDeliveryAttribute($value)
    {
        if ($value) return $value;
        
        // Calculate based on urgency and location
        $days = match($this->urgency_level) {
            4, 5 => 1, // Same day
            3 => 2,    // Next day
            2 => 3,    // 3 days
            default => 5 // Normal delivery
        };
        
        return $this->created_at->addDays($days);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'returned' => 'dark',
            default => 'light'
        };
    }

    public function getUrgencyLabelAttribute()
    {
        return match($this->urgency_level) {
            5 => 'عاجل جداً',
            4 => 'عاجل',
            3 => 'سريع',
            2 => 'عادي',
            1 => 'بطيء',
            default => 'غير محدد'
        };
    }

    // Static methods
    public static function getStatuses()
    {
        return [
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'processing' => 'قيد التجهيز',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'returned' => 'مرتجع'
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            'cash' => 'نقداً عند التسليم',
            'card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            'installment' => 'تقسيط',
            'wallet' => 'محفظة إلكترونية'
        ];
    }

    private function notifyStatusChange($status)
    {
        // Send SMS, Email, Push notifications
        // Will be implemented with notification service
    }
}
