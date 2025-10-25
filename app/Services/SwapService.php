<?php

namespace App\Services;

use App\Repositories\SwapRepository;

class SwapService
{
    /**
     * Inject SwapRepository
     */
    public function __construct(protected SwapRepository $repo) {}

    /**
     * Tính toán swap, lưu vào DB và trả về kết quả cùng 10 dòng gần nhất
     *
     * @param array $data
     * @return array
     */
    public function calculateAndStore(array $data): array
    {
        // Xác định swap rate dựa trên position type
        $swapRate = $data['position_type'] === 'Long'
            ? $data['swap_long']
            : $data['swap_short'];

        // Tính tổng swap
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

        // Lấy 10 dòng gần nhất
        $recentRecords = $this->repo->getRecent();

        // Chuẩn bị dữ liệu trả về
        $resultData = [
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'position_type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'holding_days' => $data['holding_days'],
            'totalSwap' => $totalSwap,
        ];

        // Thông báo swap dương/âm
        $notification = $totalSwap < 0
            ? ['message' => 'Swap âm, cân nhắc không nên giữ lệnh lâu', 'type' => 'danger']
            : ['message' => 'Swap dương, có thể giữ lệnh lâu', 'type' => 'success'];

        return [
            'data' => $resultData,
            'notification' => $notification,
            'recentRecords' => $recentRecords,
        ];
    }

    /**
     * Lấy lịch sử swap gần nhất
     *
     */
    public function getHistory()
    {
        return $this->repo->getRecent();
    }

    /**
     * Xóa swap theo ID
     *
     * @param int $id
     * @return bool
     */
    public function deleteSwap($id): bool
    {
        return $this->repo->delete($id);
    }

    /**
     * Tính toán swap (API) và lưu vào DB
     * Trả về dữ liệu và thông báo đơn giản
     *
     * @param array $data
     * @return array
     */
    public function calculate(array $data): array
    {
        $swapRate = $data['position_type'] === 'Long'
            ? $data['swap_long']
            : $data['swap_short'];

        $totalSwap = $data['lot_size'] * $swapRate * $data['holding_days'];

        // Lưu vào DB
        $this->repo->store([
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'days' => $data['holding_days'],
            'total_swap' => $totalSwap,
        ]);

        $resultData = [
            'pair' => $data['pair'],
            'lot_size' => $data['lot_size'],
            'position_type' => $data['position_type'],
            'swap_rate' => $swapRate,
            'holding_days' => $data['holding_days'],
            'totalSwap' => $totalSwap,
        ];

        $message = $totalSwap < 0
            ? 'Swap âm, cân nhắc không nên giữ lệnh lâu'
            : 'Swap dương, có thể giữ lệnh lâu';

        return [
            'data' => $resultData,
            'message' => $message,
        ];
    }

    /**
     * Lấy tất cả lịch sử swap
     *
     * @return array
     */
    public function getAllHistory(): array
    {
        return $this->repo->getAll();
    }

    /**
     * Dữ liệu tổng phí swap theo cặp tiền (thống kê)
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        $data = $this->repo->getTotalSwapLast7Days();

        return [
            'allData' => $data,
            'pairs' => $data->pluck('pair'),
            'totals' => $data->pluck('total_swap'),
        ];
    }
}
