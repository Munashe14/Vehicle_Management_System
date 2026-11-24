<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_number',
        'make',
        'model',
        'year',
        'vehicle_type',
        'capacity',
        'odometer_start',
        'status',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function assignments()
    {
        return $this->hasMany(DriverAssignment::class);
    }

    public function reports()
    {
        return $this->hasMany(VehicleReport::class);
    }
}