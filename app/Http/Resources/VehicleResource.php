<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            '_id' => (string)$this->_id,
            'type' => isset($this->type) ? (string)$this->type : null,
            'brand' => isset($this->brand) ? (string)$this->brand : null,
            'model' => isset($this->model) ? (string)$this->model : null
        ];
    }
}
