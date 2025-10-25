<?php

namespace App\Http\Controllers;

use App\Services\SwapService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected SwapService $service) {}

    public function index()
    {
        $history = $this->service->getHistory();
        return view('backend.swap.index', compact('history'));
    }
}
