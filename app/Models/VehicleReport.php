<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleReport extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'type',
        'description',
        'status',
        'resolution',
        'reviewed_by',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
