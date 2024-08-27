<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\Auth\PasswordResetTokens;
use App\Models\User;
use App\Notifications\Auth\PasswordRecoveryLinkNotification;
use App\Notifications\Auth\PasswordResetedNotification;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    use ResponseTrait;


    public function login(Request $request)
    {
        $credentials =  $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->server_response_error("El email o la contraseña no son válidos", null);
        }

        return $this->server_response_ok("Ok Inicio Sesion", [
            "token" => $token,
            "user" => new UserResource(auth()->user())
        ]);
    }

    public function password_reset_link(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users,email"
        ]);

        $email = $request->email;

        $token_password = rand(1111, 9999);
        $user = User::where('email', $email)->first();

        PasswordResetTokens::where("email", $email)->delete();

        PasswordResetTokens::create([
            "email" => $email,
            "token" => $token_password,
        ]);

        $user->notify(new PasswordRecoveryLinkNotification($token_password));

        return $this->server_response_ok("Se ha enviado un correo a " . $email . " con un token para cambiar la contraseña", null);
    }

    public function password_reseted(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users,email",
            "token" => "required|string",
            "password" => ["required", "confirmed", Password::min(8)]
        ]);

        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        $token_exists = PasswordResetTokens::where("email", $email)->where("token", $token)->first();

        if (!$token_exists) {
            return $this->server_response_error("El token ingresado es inválido", null, 400);
        }

        if (Carbon::parse($token_exists->created_at)->diffInMinutes() > 60) {
            return $this->server_response_error("Token vencido, debe solicitar un nuevo token", null, 400);
        }

        $user = User::where('email', $email)->first();
        $user->update(["password" => $password]);
        PasswordResetTokens::where("email", $email)->delete();

        $user->notify(new PasswordResetedNotification());

        return $this->server_response_ok("La contraseña ha sido cambiada", null, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
