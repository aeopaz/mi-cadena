<?php

namespace App\Models\SavingChain;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingChain extends Model
{
    use HasFactory, SoftDeletes;

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

    public function participants()
    {
       return $this->hasMany(Participant::class);
    }

    public function user_creator()
    {
       return $this->belongsTo(User::class,"creator_id");
    }
}
