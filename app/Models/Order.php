<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    const TABLE = 'orders';
    protected $table = self::TABLE;
    protected $fillable = [
        'customer_id',
        'description',
        'total',
        'order_id',
        'product_id',
        'sku_id',
        'quantity',
        'price',
        'status',
        'code',
        'address',
    ];
    public $timestamps = true;

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function modelFilter(): ?string
    {
        return $this->provideFilter(OrderFilter::class);
    }

    public function childrenOrders()
    {
        return $this->hasMany(Order::class)->with('orders');
    }
}
