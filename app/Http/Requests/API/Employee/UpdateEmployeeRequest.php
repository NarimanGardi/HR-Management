<?php

namespace App\Http\Requests\API\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:employees,email,' . $this->employee->id],
            'age' => ['sometimes', 'integer', 'between:18,70'],
            'salary' =>['sometimes', 'integer'],
            'gender' => ['sometimes', 'integer', 'between:1,2'],
            'hired_date' => ['sometimes', 'date'],
            'job_id' => ['sometimes', 'integer', 'exists:employee_jobs,id'],
            'manager_id' => ['integer', 'exists:employees,id' , 'nullable']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'gender.between' => 'Invalid Gender Type. Insert number only, 1 for Male, 2 for Female',
        ];
    }
}
