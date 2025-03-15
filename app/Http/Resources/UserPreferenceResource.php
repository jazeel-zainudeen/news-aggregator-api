<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     title="User Preference Resource",
     *     schema="UserPreferenceResource",
     *     type="object",
     *
     *     @OA\Property(property="preferable_id", type="int", example="1"),
     *     @OA\Property(property="preferable_type", type="string", example="category"),
     *     @OA\Property(property="name", type="string", example="Business")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'preferable_id' => $this->preferable_id,
            'preferable_type' => $this->preferable_type,
            'name' => $this->preferable->name,
        ];
    }
}
