<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    protected $table = 'employee_jobs';
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function employee() {
        return $this->hasMany(Employee::class);
    }
}
