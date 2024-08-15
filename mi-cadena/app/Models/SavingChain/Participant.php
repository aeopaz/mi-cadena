<?php

namespace App\Models\SavingChain;

use App\Models\SavingChain\SavingChain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = "participants";

    protected $fillable = [
        'saving_chain_id',
        'user_id',
        'joined_at',
        'role',
        'turn_order',
        'status',
    ];

    public function saving_chain()
    {
      return  $this->belongsTo(SavingChain::class);
    }

    public function user()
    {
      return  $this->belongsTo(User::class);
    }

    public function contributions()
    {
      return $this->hasMany(Contribution::class);
    }
}
