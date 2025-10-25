<?php

namespace App\Repositories;

use App\Models\SwapCalculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SwapRepository
{
    protected $model;
    public function __construct()
    {
        $this->model = new SwapCalculation();
    }
    public function store(array $data): SwapCalculation
    {
        return $this->model::create($data);
    }

    public function getRecent(int $limit = 10)
    {
        // return $this->model::orderByDesc('created_at')->take($limit)->get()->toArray(); //array

        // return $this->model::orderByDesc('created_at')->take($limit)->get(); //colection

        // chuyen doi ve mang object
        $records = $this->model::orderByDesc('created_at')->take($limit)->get();
        $arrayOfObjects = $records->map(function ($item) {
            return (object) $item->toArray(); // mỗi record thành object
        })->all();
        return $arrayOfObjects;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }


    public function delete($id): bool
    {
        $swap = $this->find($id);
        return $swap ? $swap->delete() : false;
    }

    public function getAll()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    // thong ke
    /**
     * Lấy tổng phí swap theo từng cặp tiền trong 7 ngày gần nhất
     */

    /**
     * SELECT 
            pair, 
            SUM(total_swap) AS total_swap
        FROM 
            swap_calculations
        WHERE 
            created_at >= NOW() - INTERVAL 7 DAY
        GROUP BY 
            pair
        ORDER BY 
            pair ASC;

     */
    public function getTotalSwapLast7Days()
    {
        return SwapCalculation::select('pair', DB::raw('SUM(total_swap) as total_swap'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('pair')
            ->orderBy('pair')
            ->get();
    }

    
}
