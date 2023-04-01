<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Jobs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'age' => $this->faker->numberBetween(18, 65),
            'hired_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'gender' => $this->faker->numberBetween(1, 2), // 1 = male , 2 = female
            'salary' => $this->faker->numberBetween(1000, 10000),
            'job_id' => Jobs::inRandomOrder()->first()->id,
        ];

    }


    public function configure()
    {
        return $this->afterCreating(function (Employee $employee) {
           if($employee->id == 1) {
               $employee->manager_id = null;
               $employee->save();
           }
        else
        {
            $employee->manager_id = Employee::inRandomOrder()->where('id', '!=', $employee->id)->first()->id;
            $employee->save();
        }
        });
        
    }
}
