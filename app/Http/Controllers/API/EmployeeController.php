<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Employee\StoreEmployeeRequest;
use App\Http\Requests\API\Employee\UpdateEmployeeRequest;
use App\Http\Resources\API\Employee\EmployeeCollectionResource;
use App\Http\Resources\API\Employee\EmployeeResource;
use App\Jobs\ImportEmployeesJob;
use App\Models\Employee;
use App\Notifications\SalaryChangedNotification;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Database\QueryException;

class EmployeeController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('job', 'manager')->get();
        return EmployeeCollectionResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $checkFounderExists = Employee::where('manager_id', null)->first();
        if ($request->manager_id == null && $checkFounderExists) {
            return $this->errorResponse('manager_id cant be null, There is already a founder', 400);
        }
        $employee = Employee::create($request->validated());
        return new EmployeeResource($employee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $checkFounderExists = Employee::where('manager_id', null)->first();
        if ($request->manager_id == null && $checkFounderExists && $employee->manager_id != null) {
            return $this->errorResponse('manager_id cant be null, There is already a founder', 400);
        }

        if ($request->manager_id == $employee->id) {
            return $this->errorResponse('employees cant be their own manager', 400);
        }

        $employee->update($request->validated());

        if ($employee->wasChanged('salary')) {
            $employee->notify(new SalaryChangedNotification($employee));
        }

        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try{
            $employee->destroy($employee->id);
            return response(null, 204);
        }
        catch(QueryException $e){
            return $this->errorResponse('Cant delete an employee who is a manager of another employee', 500);
        }
    }

    /**
     * Get all managers for specific employee
     */

    public function getManagers($id){
        try{
            $employee = Employee::with('manager')->find($id);
            $managers = collect([$employee->name]);
            while (!$employee->isFounder()) {
                $managers->push($employee->manager->name);
                $employee = $employee->manager;
            }
            return $managers->reverse();
        }
        catch(\Exception $e){
            return $this->errorResponse('Something went wrong ', 400);
        }
    }

    /**
     * Get all managers salary for specific employee
     */

     public function getManagerSalary($id){
        try{
            $employee = Employee::with('manager')->find($id);
            $managers = collect([$employee]);
            while (!$employee->isFounder()) {
                $managers->push($employee->manager);
                $employee = $employee->manager;
            }
            $result = [];
            foreach ($managers->reverse() as $manager) {
                $result[$manager->name] = $manager->salary;
            }
            return $result;
        }
        catch(\Exception $e){
            return $this->errorResponse('Something went wrong: ' .$e->getMessage(), 500);
        }
     }

    /**
    * Search employees by name
    */
    
    public function SearchEmployees(Request $request){
            $q = $request->input('q');
            $employees = Employee::nameContains($q)->get();
            return EmployeeCollectionResource::collection($employees);
    }

    /**
     * Export employees to csv
    */

    public function ExportEmployees(){
       
        $employees = Employee::all();
    
        $filename = "employees.csv";
        $handle = fopen($filename, 'w');
        foreach ($employees as $employee) {
        $line = [
                $employee->name, $employee->email, $employee->age, $employee->hired_date, $employee->salary, $employee->gender, $employee->job_id, $employee->manager_id
            ];
            fputcsv($handle, $line);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend();
    }

    /**
     * Import employees from csv
    */

    public function ImportEmployees(Request $request){
        try{
            if(!$request->hasFile('file') || $request->file('file')->getClientOriginalExtension() != 'csv'){
                return $this->errorResponse('Please upload a csv file', 400);
            }
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));

            ImportEmployeesJob::dispatch($data);

            return $this->successResponse('Employees data has been queued for processing.', 200);
        }
        catch(\ErrorException $e){
            return $this->errorResponse('Something went wrong: ' .$e->getMessage(), 500);
        }
    }
}
