@extends('backend.master')

@section('title', 'Create Category')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Create Swap</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Create Swap
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Khung hiển thị lỗi chung -->
<div id="error-messages" class="alert alert-danger" style="display:none;">
    <ul></ul>
</div>


<form method="POST" action="{{ route('admin.swap.calculate') }}" id="swap-submit">
    @csrf
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <label>Currency Pair</label>
            <select name="pair" class="custom-select form-control">
                @foreach ($Currencies as $Currency)
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
            <select name="position_type" class="custom-select form-control">
                <option value="Long" {{ old('position_type') == 'Long' ? 'selected' : '' }}>Long</option>
                <option value="Short" {{ old('position_type') == 'Short' ? 'selected' : '' }}>Short</option>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Calculate</button>
</form>

<!-- Khung hiển thị kết quả sau form -->
<div id="swap-result" class="mt-3"></div>


<!-- khung hien thi 10 dong gan nhat -->
 
<div id="history-result" class="mt-4">
    @if(count($histories) > 0)
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Currency Pair</th>
                <th>Lot Size</th>
                <th>Type</th>
                <th>Swap Rate</th>
                <th>Days</th>
                <th>Total Swap (USD)</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody id="swap-table-body">
            @foreach ($histories as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->pair }}</td>
                <td>{{ $record->lot_size }}</td>
                <td>{{ $record->type }}</td>
                <td>{{ $record->swap_rate }}</td>
                <td>{{ $record->days }}</td>
                <td>{{ $record->total_swap }}</td>
                <td>{{ $record->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>




@endsection

@section('script')
<script>
    // Hàm render bảng từ dữ liệu trả về
    function renderHistoryTable(data) {
        var records = data.recentRecords;

        var html = `
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Currency Pair</th>
                    <th>Lot Size</th>
                    <th>Position Type</th>
                    <th>Swap Rate</th>
                    <th>Days</th>
                    <th>Total Swap</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
    `;

        $.each(records, function(index, record) {
            html += `
            <tr>
                <td>${index + 1}</td>
                <td>${record.pair}</td>
                <td>${record.lot_size}</td>
                <td>${record.type}</td>
                <td>${record.swap_rate}</td>
                <td>${record.days}</td>
                <td>${record.total_swap}</td>
                <td>${record.created_at}</td>
            </tr>
        `;
        });

        html += `
            </tbody>
        </table>
    `;

        $('#history-result').html(html);
    }


    function displaySwapResult(response) {

        var notification = response.notification;
        // Lấy dữ liệu từ response
        var data = response.data;
        var totalSwap = data.totalSwap;
        var pair = data.pair;
        var lotSize = data.lot_size;
        var positionType = data.position_type;
        var swapRate = data.swap_rate;
        var days = data.holding_days;
        

        // Tạo HTML hiển thị kết quả
        var html = '<div class="alert alert-' + notification['type'] + '">';
        html += notification['message'] + '</div>';

        html += '<table class="table table-bordered mt-3">';
        html += '<thead><tr>';
        html += '<th>Currency Pair</th>';
        html += '<th>Lot Size</th>';
        html += '<th>Type</th>';
        html += '<th>Swap Rate</th>';
        html += '<th>Days</th>';
        html += '<th>Total Swap</th>';
        html += '</tr></thead>';

        html += '<tbody><tr>';
        html += '<td>' + pair + '</td>';
        html += '<td>' + lotSize + '</td>';
        html += '<td>' + positionType + '</td>';
        html += '<td>' + swapRate + '</td>';
        html += '<td>' + days + '</td>';
        html += '<td>' + totalSwap + '</td>';
        html += '</tr></tbody></table>';

        // Gắn HTML vào DOM
        $('#swap-result').html(html);
    }




    $('#swap-submit').on('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#error-messages').hide().find('ul').empty();
                $('#swap-result').empty();
            },
            success: function(response) {
                if (response.status == 1) {
                    toastr.success(response.msg);
                    // tra ve form submit
                    displaySwapResult(response.data);
                    // tra ve bang lich su
                    renderHistoryTable(response.data);
                    form.reset();
                } else {
                    toastr.error(response.msg);
                    if (response.errors) {
                        var ul = $('#error-messages ul');
                        $.each(response.errors, function(key, value) {
                            ul.append('<li>' + value[0] + '</li>');
                        });
                        $('#error-messages').show();
                    }
                }
            },
            error: function(xhr) {
                toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                console.log(xhr.responseText);
            }
        });
    });
</script>
@endsection