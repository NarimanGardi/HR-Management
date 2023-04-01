<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Jobs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobTest extends TestCase
{
    use WithFaker;
    public function create_job(){
        $job = Jobs::Create([
            'name' => $this->faker->jobTitle,
        ]);
        return $job;
    }

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

    public function create_employee($jobId){
        $employee = Employee::withoutEvents(function () use ($jobId) {
            return Employee::factory()->create([
                'job_id' => $jobId,
            ]);
        });
        return $employee;
    }

    public function test_job_index_failed_unauthenticate()
    {
        $response = $this->getJson(route('jobs.index'));

        $response->assertStatus(401);
    }

    public function test_job_index_auth(){

        $user = $this->create_user();

        $response = $this->actingAs($user)->getJson(route('jobs.index'));

        $response->assertStatus(200);
    }

    public function test_store_job_failed_validation(){
        $user = $this->create_user();

        // Define the job data
        $data = [
            'name' => '',
        ];

        $response = $this->actingAs($user)->postJson(route('jobs.store'), $data);

        $response->assertStatus(400);

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_store_job_failed_name_must_be_unique(){
        $user = $this->create_user();

        // Define the job data
        $data = [
            'name' => $this->faker->jobTitle,
        ];

        $response = $this->actingAs($user)->postJson(route('jobs.store'), $data);

        $response = $this->actingAs($user)->postJson(route('jobs.store'), $data);

        $response->assertStatus(400);

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_store_job_data()
    {
        $user = $this->create_user();

        // Define the job data
        $data = [
            'name' => $this->faker->jobTitle,
        ];

        $response = $this->actingAs($user)->postJson(route('jobs.store'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('employee_jobs', $data);
    }

    public function test_update_job_failed_validation(){
        $user = $this->create_user();

        $job = $this->create_job();

        $data = [
            'name' => '',
        ];

        $response = $this->actingAs($user)->putJson(route('jobs.update', $job->id), $data);

        $response->assertStatus(400);

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_update_job_name_can_be_same_as_existing(){
        $user = $this->create_user();

        $job = $this->create_job();

        $data = [
            'name' => $job->name,
        ];

        $response = $this->actingAs($user)->putJson(route('jobs.update', $job->id), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('employee_jobs', $data);
    }

    public function test_update_job_data(){
        $user = $this->create_user();

        $job = $this->create_job();

        $data = [
            'name' => $this->faker->jobTitle,
        ];

        $response = $this->actingAs($user)->putJson(route('jobs.update', $job->id), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('employee_jobs', $data);
    }

    public function test_delete_job_failed_constraint_parent_row(){
        $user = $this->create_user();

        $job = $this->create_job();

        $employee = $this->create_employee($job->id);

        $response = $this->actingAs($user)->deleteJson(route('jobs.destroy', $job->id));

        $response->assertStatus(500);

    }

    public function test_delete_job_data(){
        $user = $this->create_user();

        $job = $this->create_job();

        $response = $this->actingAs($user)->deleteJson(route('jobs.destroy', $job->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('employee_jobs', ['id' => $job->id]);
    }
}
