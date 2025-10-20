<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'due_date',
        'deadline',
        'priority',
        'progress',
        'project_id',
        'assigned_to',
        'submission_file',
        'completed_at',
    ];

    protected $casts = [
        'status' => 'string',
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getDeadlineClassAttribute()
    {
        if (!$this->deadline) return 'secondary';
        $now = now();
        return $this->deadline->lt($now) ? 'danger' : 'success';
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
