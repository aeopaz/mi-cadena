<?php

namespace App\Http\Resources\SavingChain;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingChainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'amount' => $this->resource->amount,
            'frequency' => $this->resource->frequency,
            'start_date' => $this->resource->start_date,
            'end_date' => $this->resource->end_date,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'creator_id' => $this->resource->creator_id,
            'user_creator_info' => new UserResource($this->resource->user_creator),
            'participant_count' => $this->resource->participant_count,
            $this->mergeWhen(auth()->id() == $this->resource->creator_id, [
                'participants' => $this->resource->participants->map(function ($participants) {
                    return [
                        "id" => $participants->id,
                        "saving_chain_id" => $participants->saving_chain_id,
                        "user_id" => $participants->user_id,
                        "user_info" => new UserResource($participants->user),
                        "status" => $participants->status,
                        "contributions" => $participants->contributions
                    ];
                })
            ]),
        ];
    }
}
