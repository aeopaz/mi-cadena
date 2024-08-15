<?php

namespace App\Http\Controllers\SavingChain;

use App\Http\Controllers\Controller;
use App\Http\Resources\SavingChain\SavingChainCollection;
use App\Http\Resources\SavingChain\SavingChainResource;
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
use Illuminate\Support\Facades\Log;
use PhpParser\ErrorHandler\Collecting;

class SavingChainController extends Controller
{
    use ResponseTrait, DataConfigTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'saving_chain_own' => new SavingChainCollection(auth()->user()->savings_chains),
            'saving_chain_participant' => new SavingChainCollection(auth()->user()->savings_chains_participating)
        ];

        return $this->server_response_ok("Success", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => 'required',
            "participant_count" => 'required|numeric',
            "amount" => 'required|numeric',
            "frequency" => 'required|in:0,8,15,30',
            "start_date" => 'required|date',
            "end_date" => 'required|date|after:start_date',
        ]);

        $request->merge(['creator_id' => auth()->id()]);

        $saving_chain = SavingChain::create($request->all());

        return $this->server_response_ok("La Cadena de ahorro ha sido creada correctamente", ["saving_chain" => $saving_chain]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $saving_chain = SavingChain::findOrFail($id);

        Gate::authorize('view', $saving_chain);

        return $this->server_response_ok("Success", new SavingChainResource($saving_chain));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "name" => 'required',
            "participant_count" => 'required|numeric',
            "amount" => 'required|numeric',
            "frequency" => 'required|in:0,8,15,30',
            "start_date" => 'required|date',
            "end_date" => 'required|date|after:start_date',
        ]);

        $saving_chain = SavingChain::findOrFail($id);

        Gate::authorize('update', $saving_chain);

        $saving_chain->update($request->all());

        return $this->server_response_ok("La Cadena de ahorro ha sido actualizada", ["saving_chain" => $saving_chain]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $saving_chain = SavingChain::findOrFail($id);
        Gate::authorize('delete', $saving_chain);
        $saving_chain->delete();

        return $this->server_response_ok("La Cadena de ahorro ha sido eliminada", null);
    }
}
