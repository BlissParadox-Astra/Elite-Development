<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanceledOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'quantity',
        'total',
        'canceled_date',
        'user_id',
        'reason',
        'action_taken',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canceledTransaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id')->withTrashed();
    }
}
