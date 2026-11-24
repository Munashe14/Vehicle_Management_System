<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceEvent extends Model
{
    protected $guarded = [];

    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}