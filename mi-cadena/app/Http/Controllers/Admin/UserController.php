<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\User\VerifyEmailNotification;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
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
