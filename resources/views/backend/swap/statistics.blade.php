@extends('backend.master')

@section('title', 'Create Category')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Swap Fee Statistics Dashboard</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        (Last 7 Days)
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Thống kê swap -->

<div class="card card-box mb-2">
    <div class="card-header">
        <h5 class="card-title">Tổng quan phí Swap (7 ngày gần nhất)</h5>
    </div>
    <div class="card-body">

        <div class="row pb-10">
            @forelse($chart['allData'] as $index => $item)
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark">{{ $item['total_swap'] }}</div>
                            <div class="font-14 text-secondary weight-500">
                                {{ $item['pair'] }}
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            @empty
                empty data
            @endforelse
        </div>

        <canvas id="myChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chart['pairs']),
            datasets: [{
                label: 'Total Swap',
                data: @json($chart['totals']),
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection