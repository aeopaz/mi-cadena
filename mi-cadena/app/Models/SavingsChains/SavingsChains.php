<?php

namespace App\Models\SavingsChains;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsChains extends Model
{
    use HasFactory;

    protected $table = "savings_chains";
    protected $fillable = [
        "name",
        "creator_id",
        "participant_count",
        "amount",
        "frequency",
        "start_date",
        "end_date",
        "status",
    ];
}
