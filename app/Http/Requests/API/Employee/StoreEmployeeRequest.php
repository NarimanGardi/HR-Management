<?php

namespace App\Http\Requests\API\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employees'],
            'age' => ['required', 'integer', 'between:18,70'],
            'salary' =>['required', 'integer'],
            'gender' => ['required', 'integer', 'between:1,2'],
            'hired_date' => ['required', 'date'],
            'job_id' => ['required', 'integer', 'exists:employee_jobs,id'],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
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
