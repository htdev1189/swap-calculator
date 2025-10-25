<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapCalculation extends Model
{
    use HasFactory;

    protected $table = 'swap_calculations';
    protected $fillable = ['pair', 'lot_size', 'type', 'swap_rate', 'days', 'total_swap'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s', // Định dạng ngày giờ khi truy xuất json
    ];
}
