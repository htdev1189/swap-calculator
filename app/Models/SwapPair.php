<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapPair extends Model
{
    use HasFactory;
    protected $fillable = ['pair', 'swap_long', 'swap_short'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
    }
}
