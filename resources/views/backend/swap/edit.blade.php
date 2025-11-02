@extends('backend.master')

@section('title', 'Create Category')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Update Swap</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Update Swap
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <ul>
        @foreach ($errors->all() as $error)
        <li>
            {{ $error }}

        </li>
        @endforeach
    </ul>
</div>
@endif


<form method="POST" action="{{ route('admin.swap.update') }}" id="swap-submit">
    @csrf
    <div class="row g-2 mb-3">
        <input type="hidden" name="id" value="{{ $swap->id }}">
        <input type="hidden" name="pair" value="{{ $swap->pair }}">
        <div class="col-md-3">
            <label>Currency Pair</label>
            <input name="pair-fixed" type="text" class="form-control" value="{{ $swap->pair }}" disabled>
        </div>
        <div class="col-md-2"><label>Lot Size</label><input name="lot_size" type="number" step="0.1" class="form-control" value="{{ $swap->lot_size }}"></div>

        <div class="col-md-2">
            <label>Swap Long</label>
            <input id="swap_long" name="swap_long" type="number" step="0.1" class="form-control"
                value="{{ $swap->type == 'Long' ? $swap->swap_rate : '' }}">
        </div>
        <div class="col-md-2">
            <label>Swap Short</label>
            <input id="swap_short" name="swap_short" type="number" step="0.1" class="form-control"
                value="{{ $swap->type == 'Short' ? $swap->swap_rate : '' }}">
        </div>




        <div class="col-md-1"><label>Days</label><input name="holding_days" type="number" class="form-control" value="{{ $swap->days }}"></div>
        <div class="col-md-2"><label>Type</label>
            <select name="position_type" class="custom-select form-control">
                <option value="Long" {{ $swap->type == 'Long' ? 'selected' : '' }}>Long</option>
                <option value="Short" {{ $swap->type == 'Short' ? 'selected' : '' }}>Short</option>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Update</button>
</form>
@endSection