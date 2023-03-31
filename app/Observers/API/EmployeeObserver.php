<?php

namespace App\Observers\API;

use App\Models\Employee;
use App\Models\EmployeeLog;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
       EmployeeLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'created',
            'employee_id' => $employee->id,
            'details' => "Employee {$employee->name} was created by user " . auth()->user()->name .".",
        ]);
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        $changes = $employee->getChanges();
        $details = '';
        foreach ($changes as $key => $value) {
            $details .= "Employee {$employee->name} {$key} was changed from {$employee->getOriginal($key)} to {$value} by user " . auth()->user()->name .". ";
        }
        EmployeeLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'updated',
            'employee_id' => $employee->id,
            'details' => $details,
        ]);
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        EmployeeLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'deleted',
            'employee_id' => $employee->id,
            'details' => "Employee {$employee->name} was deleted by user " . auth()->user()->name .".",
        ]);
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
