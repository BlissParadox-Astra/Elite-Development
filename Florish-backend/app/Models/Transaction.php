<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
      'invoice_number',
      'product_id',
      'price',
      'quantity',
      'total',
      'transaction_date',
      'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transactedProduct()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function canceledOrders()
    {
        return $this->hasMany(CanceledOrder::class, 'transaction_id', 'id');
    }
}
