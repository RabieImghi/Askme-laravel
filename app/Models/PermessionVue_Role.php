<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermessionVue_Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'role_id',
        'permession_vue_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permessionVue()
    {
        return $this->belongsTo(PermessionVue::class);
    }
}
