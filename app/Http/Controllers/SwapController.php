<?php

namespace App\Http\Controllers;

use App\Services\SwapService;
use Illuminate\Http\Request;

class SwapController extends Controller
{
    public function __construct(protected SwapService $service) {}
    public function index()
    {
        return view('backend.home', [
            'pageTitle' => 'Admin page'
        ]);
    }
    public function swap()
    {
        $Currencies = ['EURUSD', 'GBPUSD', 'USDJPY', 'XAUUSD', 'GBPJPY', 'AUDUSD'];
        $histories = $this->service->getHistory();
        return view('backend.swap.index', [
            'pageTitle' => 'Create swap calculation',
            'Currencies' => $Currencies,
            'histories' => $histories,
        ]);
    }

    public function calculate(Request $request)
    {
        // Validation
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'msg' => 'Có lỗi xảy ra!',
                'errors' => $validator->errors()
            ]);
        }

        // service
        $result = $this->service->calculateAndStore($validator->validated());

        return response()->json([
            'status' => 1,
            'msg' => 'Tính toán và lưu thành công!',
            'data' => $result
        ]);
    }

    public function history()
    {
        return view('backend.swap.history', [
            'pageTitle' => 'Swap Calculation History'
        ]);
    }

    public function getData(Request $request)
    {
        $columns = ['id', 'pair', 'lot_size', 'type', 'swap_rate', 'days', 'total_swap', 'created_at', 'updated_at'];

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
                    ->orWhere('type', 'like', "%{$searchValue}%");
            });
        }

        // Filtered records count
        $filteredRecords = $query->count();

        // Sorting
        $orderColumnIndex = intval($request->get('order')[0]['column'] ?? 0);
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        // Pagination
        $data = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $data->map(function ($row) {
            return [
                $row->id,
                $row->pair,
                $row->lot_size,
                $row->type,
                $row->swap_rate,
                $row->days,
                $row->total_swap,
                $row->created_at ? $row->created_at : '',
                // tich hop button xoa
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

    public function destroy($id)
    {
        $deleted = $this->service->deleteSwap($id);

        if ($deleted) {
            return redirect()->back()->with('success', 'Swap deleted successfully.');
        }

        return redirect()->back()->with('error', 'Swap not found.');
    }

    // statistics
    public function statistics()
    {
        $chart = $this->service->getDashboardData(); // theo cặp tiền
        return view('backend.swap.statistics', [
            'chart' => $chart,
            'pageTitle' => 'Swap Statistics'
        ]);
    }
}
