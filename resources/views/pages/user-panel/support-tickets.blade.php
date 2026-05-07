@extends('layouts.user-panel')
@section('title', 'Support Tickets — NutriBuddy Kids')
@section('panel-page-class', 'panel-userdashboard panel-tickets')

@push('styles')
<style>
    .panel-tickets .page {
        animation: fadeUp 0.5s ease-out forwards;
        background: #fff;
        min-height: 100vh;
    }

    .panel-tickets .box {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        margin-bottom: 30px;
    }

    .panel-tickets .box-head {
        padding: 22px 28px;
        border-bottom: 2px solid #e5e7eb;
        background: #fafbfc;
    }

    .panel-tickets .box-head h3 {
        font-family: 'Fredoka One', cursive;
        font-size: 1.15rem;
        color: #1f2937;
        margin: 0;
    }

    .panel-tickets .form-group {
        margin-bottom: 20px;
    }

    .panel-tickets label {
        display: block;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .panel-tickets .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-family: 'Nunito', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .panel-tickets .form-control:focus {
        border-color: #00a870;
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 168, 112, 0.1);
    }

    .panel-tickets .btn-submit {
        background: #00a870;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-family: 'Fredoka One', cursive;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .panel-tickets .btn-submit:hover {
        background: #008a5b;
        transform: translateY(-2px);
    }

    .panel-tickets .orders-table {
        width: 100%;
        border-collapse: collapse;
    }

    .panel-tickets .orders-table th {
        padding: 16px 28px;
        text-align: left;
        background: #f8f9fa;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #6b7280;
        font-weight: 800;
        border-bottom: 2px solid #e5e7eb;
    }

    .panel-tickets .orders-table td {
        padding: 20px 28px;
        border-bottom: 1.5px solid #e5e7eb;
        font-size: 0.88rem;
    }

    .panel-tickets .status-badge {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-open { background: #e0f2fe; color: #0284c7; }
    .status-in_progress { background: #fef9c3; color: #ca8a04; }
    .status-resolved, .status-closed { background: #dcfce7; color: #16a34a; }

    .priority-high { background: #fee2e2; color: #dc2626; }
    .priority-medium { background: #ffedd5; color: #ea580c; }
    .priority-low { background: #f3f4f6; color: #4b5563; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('panel-content')
    <div class="ud-main">
        <div class="page">
            
            @if(session('success'))
                <div style="background: #dcfce7; color: #16a34a; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="box">
                <div class="box-head">
                    <h3>🎫 Create New Support Ticket</h3>
                </div>
                <div style="padding: 30px;">
                    <form action="{{ route('user.support-tickets.store') }}" method="POST">
                        @csrf
                        <div class="row" style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <div class="col" style="flex: 1; min-width: 250px;">
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" name="subject" class="form-control" placeholder="Briefly describe your issue" required>
                                    @error('subject') <span style="color:red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col" style="flex: 0 0 200px;">
                                <div class="form-group">
                                    <label>Priority</label>
                                    <select name="priority" class="form-control" required>
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                    @error('priority') <span style="color:red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Message / Details</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Please provide all relevant details so we can help you faster..." required></textarea>
                            @error('message') <span style="color:red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn-submit">Submit Ticket</button>
                    </form>
                </div>
            </div>

            <div class="box">
                <div class="box-head">
                    <h3>📜 Your Support Tickets</h3>
                </div>
                <div style="overflow-x:auto">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created / Replied</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('user.support-tickets.show', $ticket) }}" style="color: #00a870; text-decoration: none; font-weight: 800;">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($ticket->status) }}">
                                            {{ str_replace('_', ' ', $ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge priority-{{ strtolower($ticket->priority) }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.8rem;">
                                            Created: {{ $ticket->created_at->format('d M, Y') }}<br>
                                            @if($ticket->last_replied_at)
                                                <span style="color:#00a870;">Replied: {{ $ticket->last_replied_at->format('d M, Y') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.support-tickets.show', $ticket) }}" style="display: inline-block; padding: 6px 12px; background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px; color: #1f2937; text-decoration: none; font-size: 0.8rem; font-weight: 700;">
                                            View Chat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">
                                        <div style="font-size: 2rem; margin-bottom: 10px;">🎉</div>
                                        You don't have any support tickets yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
