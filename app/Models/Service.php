<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'name',
        'duration',
        'price'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
}
