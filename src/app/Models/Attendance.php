<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'check_in',
        'check_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }


    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function latestPendingRequest()
    {
        return $this->hasOne(CorrectionRequest::class)
            ->where('status', 0)
            ->latestOfMany();
    }

    public function isPending()
    {
        return $this->correctionRequests()->where('status', 0)->exists();
    }
}
