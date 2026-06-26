<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = ['user_id', 'subtotal', 'tax_amount', 'tax_rate', 'total_amount', 'status', 'shipping_address'];
    protected $casts = ['subtotal' => 'decimal:2', 'tax_amount' => 'decimal:2', 'tax_rate' => 'decimal:2', 'total_amount' => 'decimal:2'];
    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
}
