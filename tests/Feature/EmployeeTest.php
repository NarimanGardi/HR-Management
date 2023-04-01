<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeLog;
use App\Models\Jobs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use WithFaker;

    public function create_user()
    {
        $email = $this->faker->unique()->safeEmail;
        $user = User::create([
           'name' => $this->faker->name,
           'email' => $email,
           'password' => bcrypt('sample123'),
        ]);
        // create token for user
        $user->token = $user->createToken('authToken')->plainTextToken;

        return $user;
    }

    public function create_employee_job(){
        $job = Jobs::Create([
            'name' => $this->faker->jobTitle,
        ]);
        return $job;
    }

    public function test_employee_index_failed_unauthenticate()
    {
        $response = $this->getJson(route('employees.index'));

        $response->assertStatus(401);
    }

    public function test_employee_index_auth(){
        
        $user = $this->create_user();

        $response = $this->actingAs($user)->getJson(route('employees.index'));

        $response->assertStatus(200);
    }

    public function test_store_employee_data()
    {
        $job = $this->create_employee_job();

        $user = $this->create_user();

        // Define the employee data
        $data = [
            'name' => 'John Doe',
            'email' => $user->email,
            'age' => 30,
            'hired_date' => '2022-04-01',
            'salary' => 1000,
            'gender' => 1,
            'job_id' => $job->id,
            'manager_id' => Employee::inRandomOrder()->first()->id ?? null,
        ];

        $response = $this->actingAs($user)->postJson(route('employees.store'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('employees', $data);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'age',
                'hired_date',
                'salary',
                'gender',
                'job_title',
            ],
        ]);
    }

    public function test_show_employee_data()
    {
        $user = $this->create_user();

        $employee = Employee::withoutEvents(function () {
            return Employee::factory()->create();
        });

        $response = $this->actingAs($user)->getJson(route('employees.show', $employee->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'age',
                'hired_date',
                'salary',
                'gender',
                'job_title',
            ],
        ]);
    }

    public function test_update_employee_failed_manager_id_cant_be_null()
    {
        // making sure that founder is exists
        Employee::withoutEvents(function () {
            Employee::factory()->create();
        });

        $job = $this->create_employee_job();
        $user = $this->create_user();

        $employee = Employee::withoutEvents(function () {
             return Employee::factory()->create();
         });

        $updatedData = [
            'name' => 'Jane Doe',
            'email' => $this->faker->unique()->safeEmail,
            'age' => 35,
            'hired_date' => '2022-04-01',
            'salary' => 1500,
            'gender' => 2,
            'job_id' => $job->id,
            
        ];

        $response = $this->actingAs($user)->patchJson(route('employees.update', $employee->id) , $updatedData);

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'message',
            'status',
            'code'
        ]);
    }

    public function test_update_employee_failed_employee_cant_be_their_own_manager()
    {
        $job = $this->create_employee_job();
        $user = $this->create_user();

        $employee = Employee::withoutEvents(function () {
             return Employee::factory()->create();
         });

        $updatedData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'age' => 60,
            'hired_date' => '2022-04-01',
            'salary' => 2500,
            'gender' => 1,
            'job_id' => $job->id,
            'manager_id' => $employee->id,
        ];

        $response = $this->actingAs($user)->patchJson(route('employees.update', $employee->id) , $updatedData);

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'message',
            'status',
            'code'
        ]);

        $this->assertDatabaseMissing('employees', $updatedData);
    }
    
    public function test_update_employee_success()
    {
        $job = $this->create_employee_job();

        $user = $this->create_user();

        $employee = Employee::withoutEvents(function () {
             return Employee::factory()->create();
         });

        $updatedData = [
            'name' => 'Jane Doe',
            'email' => $this->faker->unique()->safeEmail,
            'age' => 35,
            'hired_date' => '2022-04-01',
            'salary' => 1500,
            'gender' => 2,
            'job_id' => $job->id,
            'manager_id' => Employee::inRandomOrder()->first()->id ?? null,
            
        ];

        $response = $this->actingAs($user)->patchJson(route('employees.update', $employee->id) , $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('employees', $updatedData);

    }


    public function test_delete_employee_failed()
    {
        $user = $this->create_user();
        $job = $this->create_employee_job();
        $manager = Employee::withoutEvents(function () {
            return Employee::factory()->create();
        });

        $employee = Employee::withoutEvents(function () use ($manager) {
            return Employee::factory()->create([
                'manager_id' => $manager->id
            ]);
        });

        $response = $this->actingAs($user)->deleteJson(route('employees.destroy', $manager->id));

        $response->assertStatus(500);

        $response->assertJsonStructure([
            'message',
            'status',
            'code'
        ]);
    }

    public function test_delete_employee_success() {
        $user = $this->create_user();

        $employee = Employee::withoutEvents(function () {
            return Employee::factory()->create();
        });

        $response = $this->actingAs($user)->deleteJson(route('employees.destroy', $employee->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }

    public function test_get_employee_subordinates() {
        $user = $this->create_user();

        $manager = Employee::withoutEvents(function () {
            return Employee::factory()->create();
        });

        $employee = Employee::withoutEvents(function () use ($manager) {
            return Employee::create([
                'name' => 'Jane Doe',
                'email' => $this->faker->unique()->safeEmail,
                'age' => 35,
                'hired_date' => '2022-04-01',
                'gender' => 2,
                'salary' => 1500,
                'job_id' => $manager->job_id,
                'manager_id' => $manager->id
            ]);
        });

        $response = $this->actingAs($user)->getJson(route('api.employees.managers', $employee->id));

        $response->assertStatus(200);

    }

    public function test_employee_search(){
        $user = $this->create_user();

        Employee::withoutEvents(function () {
            Employee::factory()->count(10)->create([
                'name' => 'nariman',
            ]);
        });

        $response = $this->actingAs($user)->getJson(route('api.employees.search', ['q' => 'nariman']));

        $responseData = $response->json();

        $response->assertStatus(200);

        // data count should be 10 or greater

        $this->assertGreaterThanOrEqual(10, count($responseData['data']));

    }

    public function test_employee_logs_by_date() {
        $user = $this->create_user();

        $employee = Employee::withoutEvents(function () {
            return Employee::factory()->create();
        });

        $employeeLog = EmployeeLog::create([
            'employee_id' => $employee->id,
            'user_id' => $user->id,
            'action' => 'created',
            'details' => 'employee created', 
        ]);

        $response = $this->actingAs($user)->getJson(route('api.employees.logs', $employeeLog->created_at->format('Y-m-d')));

        $response->assertStatus(200);

        $responseData = $response->json();

        $this->assertGreaterThanOrEqual(1, count($responseData['data']));

    }

}
