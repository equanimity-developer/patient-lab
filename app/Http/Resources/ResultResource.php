<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->test_name,
            'value' => $this->test_value,
            'reference' => $this->test_reference,
        ];
    }
} 