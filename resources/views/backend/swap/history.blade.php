@extends('backend.master')

@section('title', 'Create Category')

@section('stylesheets')
<link
    rel="stylesheet"
    type="text/css"
    href="{{ asset('src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}" />
@endsection

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Swap History</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Swap History
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Simple Datatable start -->
<div class="card-box mb-30">
    <div class="pt-20 pb-20">
        <table id="historyTable" class="table stripe hover nowrap">
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
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
<!-- Datatable Setting js -->
<script src="{{ asset('vendors/scripts/datatable-setting.js') }}"></script>

<!-- Toastr notifications -->
@if (session('success'))
    <script>
        toastr.success("{{ session('success') }}");
    </script>
@endif

@if (session('error'))
    <script>
        toastr.error("{{ session('error') }}");
    </script>
@endif


<!-- datatable -->

<script>
    $('#historyTable').DataTable({
        pageLength: 5,
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= route('admin.swap.history.data') ?>',
            type: 'GET'
        },
        columns: [{
                title: '#',
                data: 0
            }, // ID
            {
                title: 'Currency Pair',
                data: 1
            }, // pair
            {
                title: 'Lot Size',
                data: 2
            }, // lot_size
            {
                title: 'Position Type',
                data: 3
            }, // type
            {
                title: 'Swap Rate',
                data: 4
            }, // swap_rate
            {
                title: 'Days',
                data: 5
            }, // days
            {
                title: 'Total Swap',
                data: 6
            }, // total_swap
            {
                title: 'Created At',
                data: 7
            }, // created_at
            // { title: 'Updated At', data: 8 },    // updated_at
            {
                title: 'Action',
                data: 8,
                orderable: false,
                searchable: false
            } // Actions

        ]
    });
</script>
@endsection