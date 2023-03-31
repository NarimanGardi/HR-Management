<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EmployeeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'employee_id',
        'details',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($log) {
            Log::channel('employee')->info($log->toJson());
        });
    }

}
