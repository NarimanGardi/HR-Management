<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
class ImportEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        
        foreach($this->data as $employee){
            Employee::withoutEvents(function () use ($employee) {
                if (! Employee::where('email', $employee[1])->exists()) {
                        Employee::create([
                            'name' => $employee[0],
                            'email' => $employee[1],
                            'age' => $employee[2],
                            'hired_date' => Carbon::parse($employee[3])->format('Y-m-d'),
                            'gender' => $employee[4],
                            'salary' => $employee[5],
                            'job_id' => $employee[6],
                            'manager_id' => $employee[7],
                    ]);
                };
            });
        }
        
    }
}
