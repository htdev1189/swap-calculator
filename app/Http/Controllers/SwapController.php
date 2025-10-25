<?php

namespace App\Http\Controllers;

use App\Services\SwapImportService;
use App\Services\SwapService;
use Illuminate\Http\Request;

class SwapController extends Controller
{
    /**
     * Inject services
     */
    public function __construct(
        protected SwapService $service,
        protected SwapImportService $swapService
    ) {}

    /**
     * Admin home page
     */
    public function index()
    {
        return view('backend.home', [
            'pageTitle' => 'Admin page'
        ]);
    }

    /**
     * Swap page - show all currencies and history
     */
    public function swap()
    {
        // Test currencies
        $dataTest = ['EURUSD', 'GBPUSD', 'USDJPY', 'XAUUSD', 'GBPJPY', 'AUDUSD'];

        // Get all currencies from service
        $Currencies = $this->swapService->getAll();

        // Get swap history
        $histories = $this->service->getHistory();


        return view('backend.swap.index', [
            'pageTitle' => 'Create swap calculation',
            'Currencies' => $Currencies,
            'histories' => $histories,
            'dataTest' => $dataTest
        ]);
    }

    /**
     * Calculate swap and store result
     */
    public function calculate(Request $request)
    {
        // Validation rules
        $validator = \Validator::make($request->all(), [
            'pair' => ['required', 'string'],
            'lot_size' => ['required', 'numeric', 'gt:0'],
            'swap_long' => ['required', 'numeric'],
            'swap_short' => ['required', 'numeric'],
            'holding_days' => ['required', 'integer', 'gt:0'],
            'position_type' => ['required', 'in:Long,Short'],
        ], [
            'pair.required' => 'Vui lòng chọn cặp tiền tệ.',
            'lot_size.required' => 'Vui lòng nhập Lot Size.',
            'lot_size.numeric' => 'Lot Size phải là số hợp lệ.',
            'lot_size.gt' => 'Lot Size phải lớn hơn 0.',
            'swap_long.required' => 'Vui lòng nhập Swap Long.',
            'swap_long.numeric' => 'Swap Long phải là số hợp lệ.',
            'swap_short.required' => 'Vui lòng nhập Swap Short.',
            'swap_short.numeric' => 'Swap Short phải là số hợp lệ.',
            'holding_days.required' => 'Vui lòng nhập số ngày nắm giữ.',
            'holding_days.integer' => 'Days phải là số nguyên hợp lệ.',
            'holding_days.gt' => 'Days phải lớn hơn 0.',
            'position_type.required' => 'Vui lòng chọn loại vị thế.',
            'position_type.in' => 'Loại vị thế chỉ được phép là Long hoặc Short.',
        ]);

        // Return validation errors if exist
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'msg' => 'Có lỗi xảy ra!',
                'errors' => $validator->errors()
            ]);
        }

        // Perform calculation and store
        $result = $this->service->calculateAndStore($validator->validated());

        return response()->json([
            'status' => 1,
            'msg' => 'Tính toán và lưu thành công!',
            'data' => $result
        ]);
    }

    /**
     * Swap history page
     */
    public function history()
    {
        return view('backend.swap.history', [
            'pageTitle' => 'Swap Calculation History'
        ]);
    }

    /**
     * Get swap data for DataTables
     */
    public function getData(Request $request)
    {
        $columns = ['id', 'pair', 'lot_size', 'type', 'swap_rate', 'days', 'total_swap', 'created_at', 'updated_at'];

        // Datatables parameters
        $draw = intval($request->get('draw'));
        $start = intval($request->get('start'));
        $length = intval($request->get('length'));
        $searchValue = trim($request->get('search')['value'] ?? '');

        $query = \DB::table('swap_calculations');

        // Total records
        $totalRecords = $query->count();

        // Search filter
        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->where('pair', 'like', "%{$searchValue}%")
                    ->orWhere('type', 'like', "%{$searchValue}%")
                    ->orWhere('lot_size', 'like', "%{$searchValue}%")
                    ->orWhere('swap_rate', 'like', "%{$searchValue}%")
                    ->orWhere('days', 'like', "%{$searchValue}%")
                    ->orWhere('total_swap', 'like', "%{$searchValue}%")
                    ->orWhere('created_at', 'like', "%{$searchValue}%");
            });
        }

        // Filtered records count
        $filteredRecords = $query->count();

        // Sorting
        
        $orderColumnIndex = intval($request->get('order')[0]['column'] ?? 0);
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        // Pagination
        $data = $query->skip($start)->take($length)->get();

        // Map data for frontend table
        $counter = $start + 1; // $start là offset của trang hiện tại
        $data = $data->map(function ($row) use (&$counter) { // kiến thức closure 
            return [
                // $row->id,
                $counter++,
                $row->pair,
                $row->lot_size,
                $row->type,
                $row->swap_rate,
                $row->days,
                $row->total_swap,
                $row->created_at ?? '',
                // Delete button with CSRF protection
                '<form action="' . route('admin.swap.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Confirm delete?\')">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-sm btn-danger">Delete</button></form>'
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
     * Delete a swap record
     */
    public function destroy($id)
    {
        $deleted = $this->service->deleteSwap($id);

        if ($deleted) {
            return redirect()->back()->with('success', 'Swap deleted successfully.');
        }

        return redirect()->back()->with('error', 'Swap not found.');
    }

    /**
     * Swap statistics page
     */
    public function statistics()
    {
        $chart = $this->service->getDashboardData(); // Chart by currency pair
        return view('backend.swap.statistics', [
            'chart' => $chart,
            'pageTitle' => 'Swap Statistics'
        ]);
    }
}
