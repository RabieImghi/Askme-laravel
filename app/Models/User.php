<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'name',
        'email',
        'password',
        'points',
        'role_id',
        'about',
        'avatar',
        'coverImage',
        'isBanne',
        'donnationLink'
    ];
    public function permission()
    {
        return $this->belongsTo(PermessionVue::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
    public function socialLink(){
        return $this->hasOne(SocialLink::class);
    }
    public function followers(){
        return $this->hasMany(Follower::class);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
