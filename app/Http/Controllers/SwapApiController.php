<?php

namespace App\Http\Controllers;

use App\Services\SwapService;
use Illuminate\Http\Request;

class SwapApiController extends Controller
{
    public function __construct(protected SwapService $service) {}

    public function api_calculate(Request $request){
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

        $swap = $this->service->calculate($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $swap
        ]);
    }

    public function api_history()
    {
        $history = $this->service->getHistory();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

}
