<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    protected $guarded = [];

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }
}