@extends('backend.master')

@section('title', 'Create Category')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Create Category</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Create Category
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>



@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<form method="POST" action="{{ route('admin.swap.calculate') }}">
    @csrf
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <label>Currency Pair</label>
            <select name="pair" class="custom-select form-control">
                @foreach ($result['Currencies'] as $Currency)
                <option value="{{ $Currency }}" {{ old('pair') == $Currency ? 'selected' : '' }}>
                    {{ $Currency }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><label>Lot Size</label><input name="lot_size" type="number" step="0.01" class="form-control" value="{{ old('lot_size') }}"></div>
        <div class="col-md-2"><label>Swap Long</label><input name="swap_long" type="number" step="0.01" class="form-control" value="{{ old('swap_long') }}"></div>
        <div class="col-md-2"><label>Swap Short</label><input name="swap_short" type="number" step="0.01" class="form-control" value="{{ old('swap_short') }}"></div>
        <div class="col-md-1"><label>Days</label><input name="holding_days" type="number" class="form-control" value="{{ old('holding_days') }}"></div>
        <div class="col-md-2"><label>Type</label>
            <select name="position_type" class="form-control">
                <option value="Long" {{ old('position_type') == 'Long' ? 'selected' : '' }}>Long</option>
                <option value="Short" {{ old('position_type') == 'Short' ? 'selected' : '' }}>Short</option>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Calculate</button>
</form>

@if(isset($result))
<div class="alert alert-{{ $result['notification']['type'] }} mt-3">
    {{ $result['notification']['message'] }}
</div>
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            @foreach($result['result'] as $field => $value)
            <th>{{ $field }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            @foreach($result['result'] as $field => $value)
            <td>{{ $value }}</td>
            @endforeach
        </tr>
    </tbody>
</table>
@endif




@endsection