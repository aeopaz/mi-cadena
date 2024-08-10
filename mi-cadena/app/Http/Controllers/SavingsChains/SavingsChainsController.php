<?php

namespace App\Http\Controllers\SavingsChains;

use App\Http\Controllers\Controller;
use App\Models\SavingsChains\SavingsChains;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class SavingsChainsController extends Controller
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
            "name" => 'required',
            "participant_count" => 'required|numeric',
            "amount" => 'required|numeric',
            "frequency" => 'required|in:0,8,15,30',
            "start_date" => 'required|date',
            "end_date" => 'required|date|after:start_date',
        ]);

        $request->merge(['creator_id' => auth()->id()]);

        $saving_chain = SavingsChains::create($request->all());

        return $this->server_response_ok("La Cadena de ahorro ha sido creada correctamente", ["saving_chain" => $saving_chain]);
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
