<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedReport extends Model
{
    protected $table = 'generated_reports';

    public $timestamps = false;

    protected $fillable = [
        'report_type',
        'report_title',
        'report_title_en',
        'date_from',
        'date_to',
        'parameters',
        'file_path',
        'file_size',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
        'generated_at' => 'datetime',
    ];

    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_CUSTOM = 'custom';

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getTitle($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->report_title : $this->report_title_en ?? $this->report_title;
    }
}
