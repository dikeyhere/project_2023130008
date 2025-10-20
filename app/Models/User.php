<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'address',
        'github',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts()
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to'); 
    }

    public function getAvatarUrlAttribute()
    {
        $path = 'avatars/' . $this->avatar;

        if ($this->avatar && file_exists(storage_path('app/public/' . $path))) {
            return asset('storage/' . $path);
        }

        return asset('storage/images/default_profile.jpg');
    }
}
