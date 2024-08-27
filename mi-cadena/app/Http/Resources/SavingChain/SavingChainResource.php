<?php

namespace App\Http\Resources\SavingChain;

use App\Http\Resources\User\UserResource;
use App\Traits\DataConfigTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class SavingChainResource extends JsonResource
{

    use DataConfigTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => Str::title($this->resource->name),
            'amount' => $this->resource->amount,
            'frequency' => $this->resource->frequency,
            'frequency_text' =>$this->frecuency_text()[$this->resource->frequency],
            'start_date' => $this->resource->start_date,
            'start_date_format' => Carbon::parse($this->resource->end_date)->format('d-M-Y'),
            'end_date' => $this->resource->end_date,
            'end_date_format' => Carbon::parse($this->resource->end_date)->format('d-M-Y'),
            'status' => $this->resource->status,
            'status_text' => $this->chains_status()[$this->resource->status],
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
                }),
                'total_contributions_paid' => $this->resource->participants->sum(function ($participants) {
                    return $participants->contributions->where('status','P')->sum('amount');
                }),
                'total_contributions_owed' => $this->resource->participants->sum(function ($participants) {
                    return $participants->contributions->where('status','D')->sum('amount');
                }),
                'late_participants'=>0
            ])
          
        ];
    }
}
