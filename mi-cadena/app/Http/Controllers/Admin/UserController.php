<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\User\VerifyEmailNotification;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|digits:10|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $user = User::create($request->all());
        $user->code_email_verify = rand(111111, 999999);
        $user->save();
        $user->notify(new VerifyEmailNotification($user->code_email_verify));

        return $this->server_response_ok("Por favor ingresa el token enviado a tu correo para confirmarlo", ["user" => $user]);
    }

    public function verify_user_email(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users,email",
            'code' => 'required',
        ]);

        $email = $request->email;
        $code = $request->code;

        $is_validate_code_user = User::where('email', $email)->where('code_email_verify', $code)->first();

        if (!$is_validate_code_user) {
            return $this->server_response_error("El código ingresado es inválido", null, 400);
        }

        $is_validate_code_user->email_verified_at = now();
        $is_validate_code_user->save();

        return $this->server_response_ok("El email ha sido confirmado", null);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return $this->server_response_ok('', ["user" => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => ['required', Rule::unique("users", "mobile")->ignore($id, "mobile")],
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        return $this->server_response_ok("Usuario actualizado",["user"=>$user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->server_response_ok("Usuario eliminado",null);
    }
}
