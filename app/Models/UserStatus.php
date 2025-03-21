<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    public function StatusBelong()
    {
        return $this->belongsTo(User::class);
    }
}
