<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
    'title',
    'description',
    'fee',
    'appointment_date',
    'appointment_time',
    'status',
    'patient_id',
    'doctor_id',
];
    //
    public function doctor() {
    return $this->belongsTo(Doctor::class);
}

public function patient() {
    return $this->belongsTo(Patient::class);
}



public function payment() {
    return $this->hasOne(Payment::class);
}
}
