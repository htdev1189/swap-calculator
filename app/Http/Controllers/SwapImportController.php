<?php

namespace App\Http\Controllers;

use App\Services\SwapImportService;
use Illuminate\Http\Request;
use Exception;

class SwapImportController extends Controller
{
    /**
     * Swap import service
     */
    protected $swapImportService;

    public function __construct()
    {
        // Khởi tạo service (có thể inject qua constructor nếu dùng DI)
        $this->swapImportService = new SwapImportService();
    }

    /**
     * Import page
     */
    public function index()
    {
        return view('backend.swap.import', [
            'pageTitle' => 'Swap Data Import',
        ]);
    }

    /**
     * Import swap data từ file CSV
     */
    public function import(Request $request)
    {
        // Validation file upload
        $validator = \Validator::make($request->all(), [
            'file' => 'required|mimes:csv'
        ], [
            'file.required' => 'Vui lòng chọn file.',
            'file.mimes' => 'Vui lòng chọn file đúng định dạng (csv).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'msg' => 'Có lỗi xảy ra!',
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Lấy đường dẫn file
            $filePath = $request->file('file')->getRealPath();

            // Gọi service import dữ liệu
            $count = $this->swapImportService->importFromCsv($filePath);

            // Trả về kết quả thành công
            return response()->json([
                'status' => 1,
                'msg' => "Import thành công {$count} dòng dữ liệu.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'msg' => 'Có lỗi xảy ra trong quá trình import: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy dữ liệu cho DataTables
     */
    public function getData(Request $request)
    {
        $columns = ['id', 'pair', 'swap_long', 'swap_short', 'created_at', 'updated_at'];

        $draw = intval($request->get('draw'));
        $start = intval($request->get('start'));
        $length = intval($request->get('length'));
        $searchValue = trim($request->get('search')['value'] ?? '');

        $query = \DB::table('swap_pairs');

        // Tổng số bản ghi
        $totalRecords = $query->count();

        // Lọc theo search nếu có
        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->where('pair', 'like', "%{$searchValue}%")
                    ->orWhere('swap_long', 'like', "%{$searchValue}%")
                    ->orWhere('swap_long', 'like', "%{$searchValue}%")
                    ->orWhere('updated_at', 'like', "%{$searchValue}%")
                    ->orWhere('created_at', 'like', "%{$searchValue}%");
            });
        }

        // Số bản ghi sau lọc
        $filteredRecords = $query->count();

        // Sắp xếp
        $orderColumnIndex = intval($request->get('order')[0]['column'] ?? 0);
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        // Phân trang
        $data = $query->skip($start)->take($length)->get();

        // Format dữ liệu cho DataTables
        $counter = $start + 1; // $start là offset của trang hiện tại
        $data = $data->map(function ($row) use (&$counter) { // kiến thức closure 
            return [
                // $row->id,
                $counter++,
                $row->pair,
                $row->swap_long,
                $row->swap_short,
                $row->created_at,
                $row->updated_at,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Lấy thông tin swap theo cặp tiền tệ
     */
    public function getPair($pair)
    {
        // Tìm theo cột pair trong bảng swap_pairs
        $swap = $this->swapImportService->getPair($pair);

        if (!$swap) {
            return response()->json([
                'status' => 0,
                'msg' => 'Cặp tiền tệ không tồn tại.'
            ]);
        }

        return response()->json([
            'swap_long' => $swap->swap_long,
            'swap_short' => $swap->swap_short,
        ]);
    }
}
