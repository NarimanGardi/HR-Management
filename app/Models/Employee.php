<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'age',
        'hired_date',
        'salary',
        'gender',
        'job_id',
        'manager_id',
    ];

    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }

    public function manager()
    {  
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function isFounder()
    {
        return is_null($this->manager_id);
    }

    public function scopeNameContains($query, $substr)
    {
        return $query->where('name', 'LIKE', '%'.$substr.'%');
    }
}