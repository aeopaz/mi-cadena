<?php

namespace App\Models\SavingChain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $table = "contributions";
    protected $fillable = [
        "participant_id",
        "amount",
        "estimated_contribution_date",
        "real_contribution_date",
        "status",
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
