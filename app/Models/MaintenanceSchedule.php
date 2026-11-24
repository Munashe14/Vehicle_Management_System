<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $guarded = [];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function type()
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }

    public function events()
    {
        return $this->hasMany(MaintenanceEvent::class);
    }
}