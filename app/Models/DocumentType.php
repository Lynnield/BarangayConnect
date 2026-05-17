<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'required_fields', 'required_attachments',
        'fee', 'processing_days', 'is_active', 'template_path',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'required_attachments' => 'array',
        'fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function templates()
    {
        return $this->hasMany(DocumentTemplate::class);
    }
}
