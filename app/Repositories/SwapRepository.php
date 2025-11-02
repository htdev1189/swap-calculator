<?php

namespace App\Repositories;

use App\Models\SwapCalculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SwapRepository
{
    /**
     * SwapCalculation model
     */
    protected $model;

    public function __construct()
    {
        $this->model = new SwapCalculation();
    }

    /**
     * Lưu swap calculation vào DB
     *
     * @param array $data
     * @return SwapCalculation
     */
    public function store(array $data): SwapCalculation
    {
        return $this->model::create($data);
    }

    /**
     * Lấy các swap calculation gần nhất
     *
     * @param int $limit
     */
    public function getRecent(int $limit = 10)
    {
        $records = $this->model::orderByDesc('created_at')
            ->take($limit)
            ->get();
        return $records;

        // Chuyển mỗi record thành object (không phải collection)
        // return $records->map(fn($item) => (object)$item->toArray())->all();
    }

    /**
     * Tìm swap theo ID
     *
     * @param int $id
     * @return SwapCalculation|null
     */
    public function find($id)
    {
        //Laravel sẽ tự throw ModelNotFoundException, và Laravel tự render trang 404.blade.php.
        return $this->model->findOrFail($id);
        // return $this->model->find($id);
    }

    /**
     * Xóa swap theo ID
     *
     * @param int $id
     * @return bool
     */
    public function delete($id): bool
    {
        $swap = $this->find($id);
        return $swap ? $swap->delete() : false;
    }

    /**
     * Lấy tất cả swap calculation
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * Thống kê tổng phí swap theo từng cặp tiền trong 7 ngày gần nhất
     *
     * SQL tương đương:
     * SELECT pair, SUM(total_swap) AS total_swap
     * FROM swap_calculations
     * WHERE created_at >= NOW() - INTERVAL 7 DAY
     * GROUP BY pair
     * ORDER BY pair ASC;
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTotalSwapLast7Days()
    {
        return SwapCalculation::select('pair', DB::raw('SUM(total_swap) as total_swap'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('pair')
            ->orderBy('pair')
            ->get();
    }

    public function updateById(int $id, array $data): bool
    {
        $swap = $this->model->find($id);

        if (!$swap) {
            return false;
        }

        return $swap->update($data);
    }
}
