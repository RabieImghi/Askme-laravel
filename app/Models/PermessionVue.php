<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermessionVue extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permession_vue__roles');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'permessionVues_users');
    }
    public function permessionVueRole()
    {
        return $this->hasMany(PermessionVue_Role::class);
    }
    public function permessionVues_users()
    {
        return $this->hasMany(permession_vues_users::class);
    }
    
}
