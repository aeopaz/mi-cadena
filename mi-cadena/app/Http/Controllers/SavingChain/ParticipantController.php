<?php

namespace App\Http\Controllers\SavingChain;

use App\Http\Controllers\Controller;
use App\Models\SavingChain\Contribution;
use App\Models\SavingChain\Participant;
use App\Models\SavingChain\SavingChain;
use App\Models\User;
use App\Notifications\SavingChain\InviteJoinSavingChain;
use App\Traits\DataConfigTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ParticipantController extends Controller
{
    use ResponseTrait, DataConfigTrait;

    public function invite_users(Request $request)
    {

        $request->validate([
            'saving_chain_id' => 'required|exists:savings_chains,id',
            'email_guest' => "required|email|exists:users,email",
            'role' => 'required|in:A,P',
            'turn_order' => 'required|numeric',
        ]);

        //TODO: Validar que el usuario que envía la invitación sea creador o administrador de la cadena

        $saving_chain = SavingChain::find($request->saving_chain_id);

        Gate::authorize('update', $saving_chain);

        $participants = $saving_chain->participants;

        if ($participants->count() >= $saving_chain->participant_count) {
            return $this->server_response_error("No se pueden agregar más participantes, cadena con el cupo lleno", null, 403);
        }


        if ($participants->where('turn_order', $request->turn_order)->count() > 0) {
            return $this->server_response_error("El turno " . $request->turn_order . " esta ocupado", null, 403);
        }

        $user_guest = User::where("email", $request->email_guest)->first();
        $request->merge(['user_id' => $user_guest->id]);
        $request->merge(['status' => 'P']);

        $participant = Participant::create($request->all());
        $participant->user->notify(new InviteJoinSavingChain($participant->saving_chain));

        return $this->server_response_ok("Se le ha enviado la invitación", ["participant" => $participant]);
    }

    public function decision_invitation(Request $request)
    {

        $request->validate([
            "participant_id" => 'required|exists:participants,id',
            "status" => 'required|in:A,R,P'
        ]);


        return   DB::transaction(function () use ($request) {
            $participant = Participant::find($request->participant_id);

            Gate::authorize('decision_invitation',  $participant);
            $payment_plan = [];

            if (in_array($participant->status, ['R', 'A'])) {
                return $this->server_response_error("La invitación ya fue" . $this->participants_status()[$request->status] . " con anterioridad.", ["participant" => $participant]);
            }

            if ($request->status == 'A') {
                $participant->update(["status" => $request->status, 'joined_at' => now()]);
                return  $payment_plan =  $this->generate_payment_plan($participant->saving_chain, $request->participant_id);
            } else {
                $participant->update(["status" => $request->status]);
            }


            return $this->server_response_ok("La invitación a unirse a la cadena fue " . $this->participants_status()[$request->status], ["participant" => $participant, 'payment_plan' => $payment_plan]);
        });
    }

    public function invitation_destroy(Request $request)
    {

        $participant = Participant::findOrFail($request->participant_id);

        Gate::authorize('update', $participant->saving_chain);

        DB::transaction(function () use ($participant) {
            $participant->contributions()->delete();
            $participant->delete();
        });

        return $this->server_response_ok("La participación ha sido eliminada", null);
    }

    public function generate_payment_plan(SavingChain $saving_chain, $participant_id)
    {

        $days = Carbon::parse($saving_chain->start_date)->diffInDays($saving_chain->end_date);
        $number_quotas = round($days / $saving_chain->frequency);

        $referenceDay = Carbon::parse($saving_chain->start_date)->copy();
        $payment_plan = collect([]);
        $payment_plan->push([
            "participant_id" => $participant_id,
            "amount" => $saving_chain->amount,
            "estimated_contribution_date" => $saving_chain->start_date,
            "status"  => "P"
        ]);

        for ($i = 0; $i < $number_quotas; $i++) {
            $estimated_contribution_date =  $this->calculate_next_date($saving_chain->frequency, $referenceDay);
            $quota = [
                "participant_id" => $participant_id,
                "amount" => $saving_chain->amount,
                "estimated_contribution_date" => $estimated_contribution_date->copy()->format("Y-m-d"),
                "status"  => "P",
            ];
            $payment_plan->push($quota);
            $referenceDay = $estimated_contribution_date->copy();
            Contribution::create($quota);
        }

        return $payment_plan;
    }

    public function calculate_next_date($frequency, $date_reference)
    {

        switch ($frequency) {
            case 1:
                $date_reference->addDays(1);
                break;
            case 7:
                $date_reference->addDays(7);
                break;
            case 15:
                $date_reference = $this->calculate_date_fortnight($date_reference);
                break;
            default: //Si es cada 30, 60, 90, 180 o 360 días
                $date_reference->addMonths($frequency / 30);
                break;
        }
        return $date_reference;
    }
    public function calculate_date_fortnight(Carbon $date_reference)
    {
        $date_reference->addDays(15);
        if ($date_reference->day <= 15) {
            $date_reference->day = 15;
        } else {
            $date_reference->endOfMonth();
        }

        return $date_reference;
    }

    public function payment_register(Request $request)
    {
        $contribution = Contribution::findOrFail($request->contribution_id);
        Gate::authorize('update', $contribution->participant->saving_chain);

        $request->merge([
            "participant_id" => $contribution->participant_id,
            "real_contribution_date" => now(),
            "status"  => "P"
        ]);


        $contribution->update($request->all());

        return $this->server_response_ok("El pago ha sido registrado", ["contribution" => $contribution]);
    }

    public function payment_reverse(Request $request)
    {
        $contribution = Contribution::findOrFail($request->contribution_id);

        Gate::authorize('update', $contribution->participant->saving_chain);

        $request->merge([
            "participant_id" => $contribution->participant_id,
            "real_contribution_date" => null,
            "status"  => "D"
        ]);


        $contribution->update($request->all());

        return $this->server_response_ok("El pago ha sido reversado", ["contribution" => $contribution]);
    }
}
