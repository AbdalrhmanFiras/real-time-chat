<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $casts = [
        'is_online' => 'boolean',
    ];
    protected $fillable = ['user_id', 'is_online', 'last_seen_at'];
    public function StatusBelong()
    {
        return $this->belongsTo(User::class);
    }
}
