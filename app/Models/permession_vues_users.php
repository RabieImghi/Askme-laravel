<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permession_vues_users extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'permession_vue_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function permessionVue()
    {
        return $this->belongsTo(PermessionVue::class);
    }
}
