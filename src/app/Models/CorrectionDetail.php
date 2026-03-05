<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_request_id',
        'type',
        'modified_time',
        'rest_id',
    ];

    public function request()
    {
        return $this->belongsTo(CorrectionRequest::class, 'correction_request_id');
    }

    public function rest()
    {
        return $this->belongsTo(Rest::class, 'rest_id');
    }
}
