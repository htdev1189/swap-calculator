@extends('backend.master')

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
                <h4>Swap Pair</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Swap Pair
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-6">
            <div class="mb-3">
                <form id="csvUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-0">
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                        <button class="btn btn-primary" type="submit">Upload CSV</button>
                    </div>
                </form>
                <span class="text-danger" id="fileError"></span>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="SwapFairsTable">
            </table>
        </div>
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
    $('#SwapFairsTable').DataTable({
        order: [
            [0, "desc"] // sap xep theo ID thu tu desc
        ],
        pageLength: 5,
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= route('admin.swap.pairs.data') ?>',
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
                title: 'Swap Long',
                data: 2
            }, // lot_size
            {
                title: 'Swap Short',
                data: 3
            },
            {
                title: 'Created At',
                data: 4
            },
            {
                'title': 'Updated At',
                data: 5
            }
        ]
    });



    // Handle CSV upload
    $('#csvUploadForm').on('submit', function(e) {

        e.preventDefault();

        var form = this;
        var formData = new FormData(form);

        $.ajax({
            url: "{{ route('admin.swap.pairs.import') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#fileError').text('');
            },
            success: function(response) {
                if (response.status == 1) {
                    toastr.success(response.msg);
                    $('#SwapFairsTable').DataTable().ajax.reload(); // reload datatable
                    form.reset();
                } else {
                    toastr.error(response.msg);
                    $('#fileError').text(response.errors.file[0]);
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