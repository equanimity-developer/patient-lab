<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'order_id',
        'test_name',
        'test_value',
        'test_reference',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
} 