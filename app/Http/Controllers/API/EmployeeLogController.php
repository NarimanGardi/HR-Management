<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Employee\EmployeeLogResource;
use App\Models\EmployeeLog;

class EmployeeLogController extends Controller
{
    public function getLogs($date)
    {
        $logs = EmployeeLog::whereDate('created_at', $date)->get();
        return EmployeeLogResource::collection($logs);
    }
}
