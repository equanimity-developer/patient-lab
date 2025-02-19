<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'surname',
        'sex',
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'sex' => Sex::class,
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
