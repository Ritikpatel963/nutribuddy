@extends('layouts.user-panel')
@section('title', 'Ticket #' . $ticket->ticket_number . ' — NutriBuddy Kids')
@section('panel-page-class', 'panel-userdashboard panel-tickets-show')

@push('styles')
    <style>
        .panel-tickets-show .page {
            animation: fadeUp 0.5s ease-out forwards;
            background: #f4f7f6;
            min-height: 100vh;
            padding-bottom: 40px;
        }

        .panel-tickets-show .header-box {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .panel-tickets-show .ticket-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: #1f2937;
            margin: 0 0 10px 0;
        }

        .panel-tickets-show .status-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-right: 10px;
        }

        .status-open {
            background: #e0f2fe;
            color: #0284c7;
        }

        .status-in_progress {
            background: #fef9c3;
            color: #ca8a04;
        }

        .status-resolved,
        .status-closed {
            background: #dcfce7;
            color: #16a34a;
        }

        .priority-high {
            background: #fee2e2;
            color: #dc2626;
        }

        .priority-medium {
            background: #ffedd5;
            color: #ea580c;
        }

        .priority-low {
            background: #f3f4f6;
            color: #4b5563;
        }

        .panel-tickets-show .ticket-replies {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        .panel-tickets-show .reply-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .panel-tickets-show .reply-header {
            padding: 15px 20px;
            background: #fafbfc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-tickets-show .reply-author {
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-tickets-show .reply-author.admin-author {
            color: #00a870;
        }

        .panel-tickets-show .reply-time {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 600;
        }

        .panel-tickets-show .reply-body {
            padding: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #1f2937;
            white-space: pre-wrap;
        }

        .panel-tickets-show .reply-box {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 25px;
        }

        .panel-tickets-show .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
            margin-bottom: 15px;
            resize: vertical;
        }

        .panel-tickets-show .form-control:focus {
            border-color: #00a870;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 168, 112, 0.1);
        }

        .panel-tickets-show .btn-submit {
            background: #00a870;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .panel-tickets-show .btn-submit:hover {
            background: #008a5b;
            transform: translateY(-2px);
        }

        .panel-tickets-show .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            text-decoration: none;
            font-weight: 700;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .panel-tickets-show .back-btn:hover {
            color: #1f2937;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('panel-content')
    <div class="ud-main">
        <div class="page" style="padding: 30px;">
            <a href="{{ route('user.support-tickets') }}" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Tickets
            </a>

            @if(session('success'))
                <div
                    style="background: #dcfce7; color: #16a34a; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="header-box">
                <div>
                    <h1 class="ticket-title">#{{ $ticket->ticket_number }} - {{ $ticket->subject }}</h1>
                    <div style="margin-top: 15px;">
                        <span
                            class="status-badge status-{{ strtolower($ticket->status) }}">{{ str_replace('_', ' ', $ticket->status) }}</span>
                        <span class="status-badge priority-{{ strtolower($ticket->priority) }}">Priority:
                            {{ $ticket->priority }}</span>
                    </div>
                </div>
                <div style="text-align: right; color: #6b7280; font-size: 0.85rem; font-weight: 600;">
                    Created: {{ $ticket->created_at->format('d M, Y H:i') }}
                </div>
            </div>

            <div class="ticket-replies">
                @foreach($ticket->messages as $msg)
                    <div class="reply-card">
                        <div class="reply-header">
                            <div class="reply-author {{ $msg->is_admin ? 'admin-author' : '' }}">
                                @if($msg->is_admin)
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    </svg>
                                    NutriBuddy Support
                                @else
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    You
                                @endif
                            </div>
                            <div class="reply-time">
                                {{ $msg->created_at->format('d M, Y H:i') }}
                            </div>
                        </div>
                        <div class="reply-body">{{ $msg->message }}</div>
                    </div>
                @endforeach
            </div>

            @if(!in_array($ticket->status, ['resolved', 'closed']))
                <div class="reply-box">
                    <h3
                        style="font-family: 'Fredoka One', cursive; font-size: 1.1rem; margin-top: 0; margin-bottom: 15px; color: #1f2937;">
                        Write a Reply</h3>
                    <form action="{{ route('user.support-tickets.reply', $ticket) }}" method="POST">
                        @csrf
                        <textarea name="message" class="form-control" rows="4" placeholder="Type your message here..."
                            required></textarea>
                        @error('message') <span
                            style="color:red; font-size: 0.8rem; display:block; margin-top:-10px; margin-bottom:10px;">{{ $message }}</span>
                        @enderror
                        <button type="submit" class="btn-submit">Send Reply</button>
                    </form>
                </div>
            @else
                <div
                    style="text-align: center; padding: 30px; background: #e0f2fe; color: #0284c7; border-radius: 20px; font-weight: 700;">
                    This ticket has been marked as {{ str_replace('_', ' ', $ticket->status) }}. If you have a new issue, please
                    create a new ticket.
                </div>
            @endif

        </div>
    </div>
@endsection