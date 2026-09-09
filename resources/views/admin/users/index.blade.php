@extends('layout.layout')

@php
    $title = 'Assign Roles to Users';
    $subTitle = 'Role & Access / Assign Role';
@endphp

@section('content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Assign Roles</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('admin.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Role & Access</li>
            <li>-</li>
            <li class="fw-medium">Assign Role</li>
        </ul>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card basic-data-table">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <h5 class="card-title mb-0">Users List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length='15'>
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Current Roles</th>
                            <th scope="col" class="text-end">Assign Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-10">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="" class="w-40-px h-40-px rounded-circle object-fit-cover">
                                        @elseif($user->google_avatar)
                                            <img src="{{ $user->google_avatar }}" alt="" class="w-40-px h-40-px rounded-circle object-fit-cover">
                                        @else
                                            <div class="w-40-px h-40-px rounded-circle bg-primary-100 text-primary-600 d-flex align-items-center justify-content-center fw-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <h6 class="text-md mb-0 fw-medium text-primary-light">{{ $user->name }}</h6>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-info-100 text-info-600 px-2 py-1 radius-4 text-xs">{{ $role->name }}</span>
                                        @endforeach
                                        @if($user->roles->count() == 0)
                                            <span class="text-secondary-light text-sm">No Role (Customer)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    @if($user->id !== 1 || auth()->id() === 1)
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 radius-8 d-inline-flex align-items-center gap-1 edit-roles-btn" data-bs-toggle="modal" data-bs-target="#editUserRoleModal" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-roles="{{ json_encode($user->roles->pluck('name')) }}">
                                            <iconify-icon icon="lucide:user-cog"></iconify-icon> Manage Roles
                                        </button>
                                    @else
                                        <span class="badge bg-secondary-100 text-secondary-600">Super Admin</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-24">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Edit User Role Modal -->
    <div class="modal fade" id="editUserRoleModal" tabindex="-1" aria-labelledby="editUserRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserRoleModalLabel">Manage Roles for <span id="modalUserName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserRoleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <h6 class="mb-16">Select Roles</h6>
                        <div class="row">
                            @foreach ($roles as $role)
                                <div class="col-12 mb-16">
                                    <div class="form-check form-check-primary">
                                        <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}">
                                        <label class="form-check-label fw-medium" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-600">Update Roles</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('dataTable')) {
            new DataTable('#dataTable', {
                paging: false,
                info: false
            });
        }

        const editModal = document.getElementById('editUserRoleModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-id');
                const userName = button.getAttribute('data-name');
                const userRoles = JSON.parse(button.getAttribute('data-roles') || '[]');

                editModal.querySelector('#modalUserName').textContent = userName;
                
                const form = editModal.querySelector('#editUserRoleForm');
                form.setAttribute('action', `/admin/users/${userId}`);

                // Check appropriate roles
                const checkboxes = editModal.querySelectorAll('.role-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = userRoles.includes(cb.value);
                });
            });
        }
    });
</script>
@endpush
