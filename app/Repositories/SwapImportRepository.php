<?php

namespace App\Repositories;

use App\Models\SwapPair;

class SwapImportRepository
{
    /**
     * SwapPair model
     */
    protected $model;

    public function __construct()
    {
        $this->model = new SwapPair();
    }

    /**
     * Lấy tất cả swap pairs
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->model::all();
    }

    /**
     * Tìm swap pair theo cột pair
     *
     * @param string $pair
     * @return SwapPair|null
     */
    public function find(string $pair)
    {
        return $this->model::where('pair', $pair)->first();
    }

    /**
     * Cập nhật hoặc tạo mới swap pair
     *
     * @param array $attributes Điều kiện để tìm record
     * @param array $values Giá trị cần cập nhật
     * @return SwapPair
     */
    public function updateOrCreate(array $attributes, array $values)
    {
        return $this->model::updateOrCreate($attributes, $values);
    }
}
