<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingChain\Participant;
use App\Models\User;
use App\Notifications\User\VerifyEmailNotification;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $request->merge(['email' => $user->email]);
        $this->send_code_verify_user_email($request);

        return $this->server_response_ok("Por favor ingresa el token enviado a tu correo para confirmarlo", ["user" => $user]);
    }


    public function send_code_verify_user_email(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users,email",
        ]);

        $user = User::where('email', $request->email)->first();

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

        return $this->server_response_ok("Usuario actualizado", ["user" => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::with('savings_chains_participating', 'savings_chains')->findOrFail($id);
     
       return  DB::transaction(function () use ($user) {

            //Operación de eliminar contribuciones y participaciones que tenga el usuario a cadenas
            if ($user->savings_chains_participating->count() > 0) {
                if ($user->savings_chains_participating->contributions) {
                    //Eliminar las contribuciones que ha realizado
                    $user->savings_chains_participating->contributions()->delete();
                }
                // Desvincular las cadenas del usuario antes de eliminarlo
                $user->savings_chains_participating()->detach();
            }
          
            //Operación de eliminar las contribuciones y participaciones que tengan usuarios con las cadenas creadas por el usuario
            if ($user->savings_chains->count() > 0) {
            return  $participants=  $user->savings_chains->load("participants");
                if ($user->savings_chains->participants) {

                    if ($user->savings_chains->participants->contributions->count() > 0) {

                        $user->savings_chains->participants->contributions()->delete();
                    }

                    $user->savings_chains->participants()->delete();
                }
                $user->savings_chains()->delete();
            }


            //Eliminar el usuario
            $user->delete();
        });


        return $this->server_response_ok("Usuario eliminado", null);
    }
}
