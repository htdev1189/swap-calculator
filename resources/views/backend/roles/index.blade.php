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
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="pd-20 card-box mb-30">
                <div class="card-headerr"><h4>Add Role</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <input type="text" name="name" placeholder="Tên role" class="form-control mb-2">
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pd-20 card-box mb-30">
                <div class="card-headerr"><h4>Add Permission</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf
                        <input type="text" name="name" placeholder="Tên quyền" class="form-control mb-2">
                        <button type="submit" class="btn btn-success">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="pd-20 card-box mb-30">
        <h4>Danh sách Role</h4>
        <ul>
            @foreach($roles as $role)
                <li>
                    <strong>{{ $role->name }}</strong> —
                    Quyền: 
                    @foreach($role->permissions as $perm)
                        <span class="badge bg-success">{{ $perm->name }}</span>
                    @endforeach
                </li>
            @endforeach
        </ul>
    </div>

    <div class="row">
        <div class="col-md-6">
            {{-- Gán quyền cho Role --}}
            <div class="pd-20 card-box mb-30">
                <div class="title mb-2"><h4>Gán quyền cho Role</h4></div>
                <form method="POST" action="{{ route('admin.roles.permissions.assign') }}">
                    @csrf
                    <select name="role_id" class="form-control custtom-select mb-2">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <select name="permissions[]" multiple class="form-control custom-select2 mb-2">
                        @foreach($permissions as $p)
                            <option value="{{ $p->name }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-info">Gán</button>
                </form>
                
                {{-- Xóa permission khỏi Role --}}
                <hr>
                <h5>Xóa Permission khỏi Role</h5>
                @foreach($roles as $role)
                    <strong>{{ $role->name }}</strong><br>
                    @foreach($role->permissions as $perm)
                        <form method="POST" action="{{ route('admin.roles.permissions.remove', $role->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="permission" value="{{ $perm->name }}">
                            <button type="submit" class="btn btn-sm btn-danger mb-1">{{ $perm->name }} xóa</button>
                        </form>
                    @endforeach
                    <hr>
                @endforeach
            </div>
        </div>

        <div class="col-md-6">
            {{-- Gán Role cho User --}}
            <div class="pd-20 card-box mb-30">
                <div class="title mb-2"><h4>Gán Role cho User</h4></div>
                <form method="POST" action="{{ route('admin.users.roles.assign') }}">
                    @csrf
                    <select name="user_id" class="form-control custom-select mb-2">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>

                    <select class="custom-select custom-select2 form-control mb-2" multiple="multiple" style="width: 100%" name="role" >
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-warning">Gán</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
