<?php

namespace App\Http\Resources\API\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
            'salary' => $this->salary,
            'gender' => $this->gender == 1 ? 'Male' : 'Female',
            'hired_date' => $this->hired_date,
            'job_title' => $this->job->name,
        ];
    }
}
