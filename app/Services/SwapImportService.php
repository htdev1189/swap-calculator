<?php

namespace App\Services;

use App\Repositories\SwapImportRepository;
use Exception;

class SwapImportService
{
    /**
     * Swap import repository
     */
    protected $repo;

    public function __construct()
    {
        // Khởi tạo repository (có thể inject nếu dùng DI)
        $this->repo = new SwapImportRepository();
    }

    /**
     * Lấy tất cả swap pairs
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function getAll()
    {
        return $this->repo->all();
    }

    /**
     * Lấy thông tin swap theo cặp tiền tệ
     *
     * @param string $pair
     * @return object|null
     */
    public function getPair(string $pair)
    {
        return $this->repo->find($pair);
    }

    /**
     * Import dữ liệu từ file CSV
     *
     * @param string $filePath
     * @return int Số dòng dữ liệu đã import
     * @throws Exception
     */
    public function importFromCsv(string $filePath): int
    {
        // Kiểm tra file tồn tại
        if (!file_exists($filePath)) {
            throw new Exception("File không tồn tại: $filePath");
        }

        // Mở file CSV
        if (($handle = fopen($filePath, 'r')) === false) {
            throw new Exception("Không thể mở file CSV.");
        }

        $header = null; // Lưu tiêu đề cột
        $count = 0;     // Đếm số dòng đã import

        // Đọc từng dòng CSV
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Dòng đầu tiên là header
            if (!$header) {
                $header = $row;
                continue;
            }

            // Kết hợp tiêu đề với dữ liệu
            $data = array_combine($header, $row);

            // Lưu hoặc cập nhật dữ liệu vào database
            $this->repo->updateOrCreate(
                ['pair' => $data['pair']],
                [
                    'swap_long' => (float)$data['swap_long'],
                    'swap_short' => (float)$data['swap_short']
                ]
            );

            $count++;
        }

        fclose($handle);
        return $count;
    }
}
