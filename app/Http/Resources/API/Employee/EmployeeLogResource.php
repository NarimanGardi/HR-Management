<?php

namespace App\Http\Resources\API\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->name ?? null,
            'action' => $this->action,
            'employee' => $this->employee->name ?? null,
            'details' => $this->details,
            'created_at' => $this->created_at,
        ];
    }
}
