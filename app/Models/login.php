<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class login extends Model
{
    protected $table = 'login';
    protected $fillable = ['email', 'password'];
}
