@extends('layout.layout')
@php
    $title = 'Support Tickets';
    $subTitle = 'Ecommerce / Support Tickets';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="card mb-24">
        <div class="card-header"><h5 class="card-title mb-0">Create Support Ticket</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ecommerce.support-tickets.store') }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Customer User</label>
                    <select name="user_id" class="form-select">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card basic-data-table">
        <div class="card-header"><h5 class="card-title mb-0">Ticket List</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                    <thead>
                        <tr>
                            <th>Ticket Details</th>
                            <th>User</th>
                            <th>Status/Priority</th>
                            <th>Last Activity</th>
                            <th class="text-end">Update Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            @php
                                $statusClass = match(strtolower($ticket->status)) {
                                    'resolved', 'closed' => 'success',
                                    'in_progress' => 'info',
                                    default => 'warning'
                                };
                                $priorityClass = match(strtolower($ticket->priority)) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    default => 'success'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-md fw-bold text-primary-600">{{ $ticket->ticket_number }}</span>
                                        <small class="text-dark fw-medium text-truncate" style="max-width: 250px">{{ $ticket->subject }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-sm text-secondary-light fw-medium">{{ $ticket->user?->name ?? 'Guest' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-{{ $statusClass }}-100 text-{{ $statusClass }}-600 w-fit px-2">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
                                        <span class="badge bg-{{ $priorityClass }}-100 text-{{ $priorityClass }}-600 w-fit px-2">{{ ucfirst($ticket->priority) }}</span>
                                    </div>
                                </td>
                                <td><span class="text-xs text-secondary-light fw-medium">{{ $ticket->last_replied_at?->format('d M Y, H:i') ?? $ticket->created_at->format('d M Y, H:i') }}</span></td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2 py-2">
                                        <form method="POST" action="{{ route('admin.ecommerce.support-tickets.update', $ticket) }}" class="d-inline-flex gap-2 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm py-1" style="width: 120px">
                                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                            <input type="text" name="admin_note" value="{{ $ticket->admin_note }}" class="form-control form-control-sm py-1" style="width: 150px" placeholder="Note...">
                                            <button type="submit" class="btn btn-sm btn-outline-success-600 radius-8 px-3">Save</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.ecommerce.support-tickets.destroy', $ticket) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger-600 radius-8 d-inline-flex align-items-center gap-1" onclick="return confirm('Delete this ticket?')">
                                                <iconify-icon icon="mingcute:delete-2-line"></iconify-icon> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }
            $('#dataTable').DataTable({
                responsive: true,
                order: [[0, 'desc']]
            });
        });
    </script>
@endsection
