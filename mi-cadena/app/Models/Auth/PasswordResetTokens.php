<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetTokens extends Model
{
    use HasFactory;

    protected $table= 'password_reset_tokens';
    protected $fillable=[
        "email",
        "token",
    ];
}
