@extends('layout.layout')

@php
    $title = 'Edit Role';
    $subTitle = 'Role & Access / Edit';
@endphp

@section('content')



    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card basic-data-table border-0 radius-12 mb-24">
        <div class="card-header bg-base border-bottom py-16 px-24">
            <h5 class="card-title mb-0">Role Information</h5>
        </div>
        <div class="card-body p-24">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Moderator" value="{{ old('name', $role->name) }}" required>
                    </div>

                    <div class="col-12 mt-24">
                        <h6 class="mb-16">Assign Permissions</h6>
                        <div class="row">
                            @php
                                $rolePermissions = $role->permissions->pluck('name')->toArray();
                            @endphp
                            @foreach ($permissions as $permission)
                                <div class="col-md-3 col-sm-4 col-6 mb-16">
                                    <div class="form-check form-check-primary">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ ucwords(str_replace('manage ', '', $permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 mt-24">
                        <button type="submit" class="btn btn-primary-600">Update Role</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
