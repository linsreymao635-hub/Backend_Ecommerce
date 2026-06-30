<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Cart;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'google_id',
        'facebook_id',
        'github_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function wishlist() {
        return $this->hasMany(Wishlist::class);
    }

    public function cart() {
        return $this->hasMany(Cart::class);
    }
}
