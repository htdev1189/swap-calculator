<?php

namespace App\Services;

use App\Repositories\SwapRepository;

class SwapService
{
    // kieu khai bao ngan gon
    public function __construct(protected SwapRepository $repo) {}


    /**
     * Tính toán swap, lưu vào DB, và trả về danh sách mới nhất (10 dòng)
     */
    public function calculateAndStore(array $data): array
    {
        // Tính toán swap rate
        $swapRate = $data['position_type'] === 'Long'
            ? $data['swap_long']
            : $data['swap_short'];

        $totalSwap = $data['lot_size'] * $swapRate * $data['holding_days'];

        // Lưu vào DB thông qua Repository
        $this->repo->store([
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'days' => $data['holding_days'],
            'total_swap' => $totalSwap,
        ]);

        // Lấy 10 dòng mới nhất
        $recentRecords = $this->repo->getRecent();

        // tra ve row moi nhat dc them vao
        $data = [
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'position_type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'holding_days' => $data['holding_days'],
            'totalSwap' => $totalSwap,
        ];


        return [
            'data' => $data,
            'notification' => $totalSwap < 0
                ? ['message' => 'Swap âm, cân nhắc không nên giữ lệnh lâu', 'type' => 'danger']
                : ['message' => 'Swap dương, có thể giữ lệnh lâu', 'type' => 'success'],
            'recentRecords' => $recentRecords,
        ];
    }
    public function getHistory(): array
    {
        return $this->repo->getRecent();
    }

    public function deleteSwap($id)
    {
        return $this->repo->delete($id);
    }

    // api
    public function calculate(array $data): array
    {

        $swapRate = $data['position_type'] === 'Long'
            ? $data['swap_long']
            : $data['swap_short'];

        $totalSwap = $data['lot_size'] * $swapRate * $data['holding_days'];

        $this->repo->store([
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'days' => $data['holding_days'],
            'total_swap' => $totalSwap,
        ]);
        $data = [
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'position_type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'holding_days' => $data['holding_days'],
            'totalSwap' => $totalSwap,
        ];

        return [
            'data' => $data,
            'message' => $totalSwap < 0
                ? 'Swap âm, cân nhắc không nên giữ lệnh lâu'
                : 'Swap dương, có thể giữ lệnh lâu',
        ];
    }
    public function getAllHistory()
    {
        return $this->repo->getAll();
    }

    // thong ke
       /**
     * Dữ liệu tổng phí swap theo cặp tiền
     */
    public function getDashboardData()
    {
        $data = $this->repo->getTotalSwapLast7Days();
        return [
            'allData' => $data,
            'pairs' => $data->pluck('pair'),
            'totals' => $data->pluck('total_swap'),
        ];
    }

}
