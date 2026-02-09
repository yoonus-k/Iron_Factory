<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerPermission extends Model
{
    use HasFactory;

    protected $table = 'worker_permissions';

    protected $fillable = [
        'worker_id',
        'permission_id',
    ];

    public $timestamps = true;

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
