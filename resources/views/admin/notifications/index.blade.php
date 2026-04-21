@extends('layout.layout')
@php
    $title = 'Notifications';
    $subTitle = 'Administrative / Notifications';
@endphp

@section('content')
    <div class="row g-4">
        <div class="col-lg-12">
            @include('admin.ecommerce._messages')

            <div class="card basic-data-table">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h5 class="card-title mb-0">Activity Feed</h5>
                    @if($notifications->whereNull('read_at')->count() > 0)
                        <form action="{{ route('admin.ecommerce.notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="lucide:check-check" class="text-xl"></iconify-icon>
                                Mark All as Read
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $isUnread = is_null($notification->read_at);
                                $icon = 'lucide:bell';
                                $bgClass = 'bg-primary-100 text-primary-600';
                                
                                if (str_contains($notification->type, 'Order')) {
                                    $icon = 'f7:bag';
                                    $bgClass = 'bg-success-100 text-success-600';
                                } elseif (str_contains($notification->type, 'Return')) {
                                    $icon = 'solar:undo-left-round-bold';
                                    $bgClass = 'bg-warning-100 text-warning-main';
                                } elseif (str_contains($notification->type, 'User') || str_contains($notification->type, 'Customer')) {
                                    $icon = 'solar:user-bold';
                                    $bgClass = 'bg-info-100 text-info-600';
                                }
                            @endphp
                            <div class="list-group-item list-group-item-action p-24 {{ $isUnread ? 'bg-primary-50' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0 w-48-px h-48-px radius-circle {{ $bgClass }} d-flex align-items-center justify-content-center text-2xl">
                                        <iconify-icon icon="{{ $icon }}"></iconify-icon>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-4">
                                            <h6 class="text-md mb-0 fw-bold {{ $isUnread ? 'text-primary-600' : 'text-secondary-light' }}">
                                                {{ $data['title'] ?? 'System Notification' }}
                                            </h6>
                                            <span class="text-xs text-secondary-light fw-medium">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm mb-12 text-secondary-light">
                                            {{ $data['message'] ?? 'You have a new activity in your store.' }}
                                        </p>
                                        
                                        <div class="d-flex align-items-center gap-3">
                                            @if($isUnread)
                                                <form action="{{ route('admin.ecommerce.notifications.read', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary px-16 py-4 radius-4 text-xs">Mark as Read</button>
                                                </form>
                                            @endif
                                            
                                            @if(isset($data['action_url']))
                                                <a href="{{ $data['action_url'] }}" class="btn btn-sm btn-primary-600 px-16 py-4 radius-4 text-xs d-inline-flex align-items-center gap-1">
                                                    View Details <iconify-icon icon="lucide:arrow-right" class="text-xs"></iconify-icon>
                                                </a>
                                            @endif

                                            <form action="{{ route('admin.ecommerce.notifications.destroy', $notification->id) }}" method="POST" class="ms-auto" onsubmit="return confirm('Delete this notification?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger-main hover-text-danger-600 text-xl d-flex align-items-center bg-transparent border-0 p-0">
                                                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-64 text-center">
                                <div class="w-80-px h-80-px radius-circle bg-secondary-100 d-inline-flex align-items-center justify-content-center text-secondary-light text-4xl mb-16">
                                    <iconify-icon icon="lucide:bell-off"></iconify-icon>
                                </div>
                                <h6 class="text-secondary-light mb-0">No notifications found.</h6>
                                <p class="text-xs text-secondary-light">We'll alert you when something important happens.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
