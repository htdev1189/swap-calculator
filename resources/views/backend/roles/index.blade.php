@extends('backend.master')

@section('content')
    <div class="container">

        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Roles & Permissions</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="http://127.0.0.1:8000/admin">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Roles & Permissions
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Hiển thị thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card card-box mb-2">
                    <div class="card-header">
                        <h5 class="card-title">Add Role</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.roles.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="">Roles</label>
                                <input type="text" name="name" placeholder="Tên role" class="form-control mb-2">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Thêm</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-box mb-2">
                    <div class="card-header">
                        <h5 class="card-title">Add Permission</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.permissions.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="">Permission</label>
                                <input type="text" name="name" placeholder="Tên quyền" class="form-control mb-2">
                            </div>
                            <button type="submit" class="btn btn-sm btn-success">Thêm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-box mb-2">
            <div class="card-header">
                <h5 class="card-title">Danh sách Role</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Roles</th>
                            <th scope="col">Permission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td scope="row">{{ $role->name }}</td>
                                <td scope="row">
                                    @foreach ($role->permissions as $perm)
                                        <span class="badge bg-success text-light per">{{ $perm->name }}<i
                                                class="remove-per" data-role="{{ $role->id }}"
                                                data-perm="{{ $perm->id }}">x</i></span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



        <div class="row">

            {{-- gán quyền cho roles --}}
            <div class="col-md-6">
                <div class="card card-box mb-2">
                    <div class="card-header">
                        <h5 class="card-title">Gán quyền cho Role</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.roles.permissions.assign') }}">
                            @csrf
                            <div class="form-group">
                                <label for="">Roles</label>
                                <select name="role_id" class="form-control custtom-select mb-2">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Permission</label>
                                <select name="permissions[]" multiple class="form-control custom-select2 mb-2">
                                    @foreach ($permissions as $p)
                                        <option value="{{ $p->name }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-info">Gán</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- gán roles cho user --}}
            <div class="col-md-6">
                <div class="card card-box mb-2">
                    <div class="card-header">
                        <h5 class="card-title">Gán roles cho User</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.roles.assign') }}">
                            @csrf
                            <div class="form-group">
                                <label for="">User</label>
                                <select name="user_id" class="form-control custom-select">
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Roles</label>
                                <select class="custom-select custom-select2 form-control" multiple="multiple"
                                    style="width: 100%" name="role">
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-warning">Gán</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('stylesheets')
    <style>
        .per {
            position: relative;
            display: inline-block;
            margin-right: 12px;
        }

        i.remove-per {
            background: red;
            width: 15px;
            height: 15px;
            display: flex;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            top: -8px;
            position: absolute;
            right: -8px;
            cursor: pointer;
        }
    </style>
@endsection


@section('script')
    <script>
        $(document).ready(function() {

            const deletePermissionUrl = "{{ route('admin.roles.remove.permission', ['role' => '__ROLE__', 'permission' => '__PERM__']) }}";

            // Khi nhấn nút xóa permission
            $(document).on('click', '.remove-per', function(e) {
                e.preventDefault();

                let roleId = $(this).data('role');
                let permId = $(this).data('perm');
                let csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Dùng route name (tạo URL bằng cách thay placeholder)
                let url = deletePermissionUrl
                    .replace('__ROLE__', roleId)
                    .replace('__PERM__', permId);

                if (confirm('Bạn có chắc chắn muốn xóa quyền này khỏi role không?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            // alert(response.message);
                            location.reload(); // reload lại trang (hoặc bạn có thể cập nhật DOM mà không reload)
                            toast.success(response.message);
                        },
                        error: function(xhr) {
                            toast.error('Có lỗi xảy ra, vui lòng thử lại!');
                            // alert('Có lỗi xảy ra, vui lòng thử lại!');
                        }
                    });
                }
            });

        });
    </script>
@endsection
