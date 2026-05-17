<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'report_name', 'report_type', 'generated_by', 'file_path', 'file_format',
        'filters', 'status',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function generatedByUser()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
