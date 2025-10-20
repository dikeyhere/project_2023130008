<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'deadline', 'priority', 'status', 'created_by', 'user_id', 'team_leader_id'];

    protected $casts = [
        'status' => 'string',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function getProgressAttribute()
    {
        $total = $this->tasks->count();
        $done = $this->tasks->where('status', 'Completed')->count();
        return $total > 0 ? round(($done / $total) * 100) : 0;
    }

    public function scopeAccessible($query)
    {
        if (auth()->user()->role === 'anggota_tim') {
            return $query->whereHas('tasks', function ($q) {
                $q->where('assigned_to', auth()->id());
            });
        }
        return $query;
    }
}
