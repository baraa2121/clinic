<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'specialization',
        'bio',
        'consultation_fee',
        'experience_years',
        'is_approved',
        'image',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
{
    return $this->hasMany(Service::class);
}

public function reviews()
{
    return $this->hasMany(Review::class);
}

public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function chats()
{
    return $this->hasMany(Chat::class);
}
}