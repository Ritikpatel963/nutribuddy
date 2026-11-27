@extends('layouts.user-panel')
@section('title', 'My Returns — NutriBuddy Kids')
@section('panel-page-class', 'panel-returns')
@section('panel-content')

    <div class="page">
        @if(session('success'))
            <div class="alert alert-success" style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="fade-in d1">
            @forelse($returns as $return)
                <div class="return-item-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                <span style="font-family: 'Fredoka One', cursive; font-size: 1.3rem; color: var(--dk);">#{{ $return->return_number }}</span>
                                <!-- Status Dropdown -->
                                <div style="position: relative; display: inline-block;">
                                    <button type="button" onclick="toggleStatusDropdown(this)" 
                                        class="status-badge {{ $return->status === 'completed' ? 's-delivered' : ($return->status === 'rejected' ? 's-cancelled' : 's-pending') }}" 
                                        style="font-size: 0.75rem; padding: 6px 14px; border-radius: 50px; font-weight: 800; letter-spacing: 0.5px; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; background: inherit; color: inherit;">
                                        {{ strtoupper($return->status) }}
                                        <svg class="return-page-icon" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6" /></svg>
                                    </button>
                                    <div class="status-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1.5px solid var(--border); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); min-width: 200px; z-index: 1000; margin-top: 8px;">
                                        <div style="padding: 12px 0;">
                                            <div class="status-option" data-status="pending" style="padding: 12px 20px; cursor: pointer; transition: all 0.2s; {{ $return->status === 'pending' ? 'background: var(--pkl); color: var(--pk);' : 'color: #555;' }}" onmouseover="this.style.background='var(--cr)'" onmouseout="this.style.background='{{ $return->status === 'pending' ? 'var(--pkl)' : 'transparent' }}'">
                                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg> Pending
                                                <span style="display: block; font-size: 0.7rem; color: #999; margin-top: 2px;">Waiting for approval</span>
                                            </div>
                                            <div class="status-option" data-status="approved" style="padding: 12px 20px; cursor: pointer; transition: all 0.2s; {{ $return->status === 'approved' ? 'background: var(--mnl); color: var(--mn);' : 'color: #555;' }}" onmouseover="this.style.background='var(--cr)'" onmouseout="this.style.background='{{ $return->status === 'approved' ? 'var(--mnl)' : 'transparent' }}'">
                                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9" /><path d="m7.5 12.5 3 3 6-7" /></svg> Approved
                                                <span style="display: block; font-size: 0.7rem; color: #999; margin-top: 2px;">Return approved & in progress</span>
                                            </div>
                                            <div class="status-option" data-status="rejected" style="padding: 12px 20px; cursor: pointer; transition: all 0.2s; {{ $return->status === 'rejected' ? 'background: #ffebee; color: #e11d48;' : 'color: #555;' }}" onmouseover="this.style.background='var(--cr)'" onmouseout="this.style.background='{{ $return->status === 'rejected' ? '#ffebee' : 'transparent' }}'">
                                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9" /><path d="m9 9 6 6m0-6-6 6" /></svg> Rejected
                                                <span style="display: block; font-size: 0.7rem; color: #999; margin-top: 2px;">Return request rejected</span>
                                            </div>
                                            <div class="status-option" data-status="completed" style="padding: 12px 20px; cursor: pointer; transition: all 0.2s; {{ $return->status === 'completed' ? 'background: var(--mnl); color: var(--mn);' : 'color: #555;' }}" onmouseover="this.style.background='var(--cr)'" onmouseout="this.style.background='{{ $return->status === 'completed' ? 'var(--mnl)' : 'transparent' }}'">
                                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="14" rx="2" /><path d="M3 10h18" /></svg> Refund Processed
                                                <span style="display: block; font-size: 0.7rem; color: #999; margin-top: 2px;">Completed & refund sent</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <p style="margin: 0; color: #555; font-size: 0.95rem; font-weight: 600;">
                                    Reference Order: <a href="{{ route('user.orders.detail-page', $return->order_id) }}" style="color: var(--pk); text-decoration: none; border-bottom: 1px dashed var(--pk);">#{{ $return->order->order_number }}</a>
                                </p>
                                <p style="margin: 0; font-size: 0.85rem; color: #999; display: flex; align-items: center; gap: 5px;">
                                    <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 11h18" /></svg> Requested on {{ $return->created_at->format('d M Y, h:i A') }}
                                </p>
                            </div>
                        </div>
                        <div style="text-align: right; background: var(--cr); padding: 12px 20px; border-radius: 15px; border: 1.5px solid var(--border);">
                            <div style="font-family: 'Fredoka One', cursive; color: var(--pk); font-size: 1.4rem; margin-bottom: 2px;">
                                ₹{{ number_format($return->refund_amount, 2) }}
                            </div>
                            <span style="font-size: 0.7rem; color: #888; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Refund Amount</span>
                        </div>
                    </div>

                    <div style="background: #fdfdfd; border-radius: 18px; padding: 20px; margin-bottom: 20px; border: 1.5px solid var(--border); position: relative;">
                        <!-- Return Status Timeline with Dropdown -->
                        <div style="margin-bottom: 20px;">
                            <strong style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #aaa; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px;">
                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" /></svg> Return Progress
                            </strong>
                            <div style="background: white; border: 1.5px solid var(--border); border-radius: 14px; padding: 20px; position: relative;">
                                <!-- Dropdown Select -->
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #666; margin-bottom: 10px;">Current Status:</label>
                                    <select id="returnStatusSelect-{{ $return->id }}" style="width: 100%; padding: 12px 16px; border: 2px solid var(--border); border-radius: 10px; font-size: 1rem; font-weight: 700; color: var(--dk); background: white; cursor: pointer; transition: all 0.2s;" onchange="updateReturnStatus({{ $return->id }}, this.value)">
                                        <option value="pending" {{ $return->status === 'pending' ? 'selected' : '' }}>Pickup Scheduled</option>
                                        <option value="approved" {{ $return->status === 'approved' ? 'selected' : '' }}>Pickup Done</option>
                                        <option value="in_transit" {{ $return->status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="received" {{ $return->status === 'received' ? 'selected' : '' }}>Product Received</option>
                                        <option value="completed" {{ $return->status === 'completed' ? 'selected' : '' }}>Refund Processed</option>
                                    </select>
                                </div>

                                <!-- Visual Timeline -->
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                    @php
                                        $stages = [
                                            'pending' => ['name' => 'Pickup Scheduled', 'color' => '#fff3cd'],
                                            'approved' => ['name' => 'Pickup Done', 'color' => '#d1e7dd'],
                                            'in_transit' => ['name' => 'In Transit', 'color' => '#cfe2ff'],
                                            'received' => ['name' => 'Product Received', 'color' => '#e2e3e5'],
                                            'completed' => ['name' => 'Refund Processed', 'color' => '#d1e7dd'],
                                        ];
                                        $stageOrder = ['pending', 'approved', 'in_transit', 'received', 'completed'];
                                        $currentIndex = array_search($return->status, $stageOrder);
                                    @endphp
                                    
                                    @foreach($stageOrder as $index => $stage)
                                        @php
                                            $stageInfo = $stages[$stage];
                                            $isCompleted = $index <= $currentIndex;
                                            $isCurrent = $index === $currentIndex;
                                        @endphp
                                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
                                            <!-- Stage Circle -->
                                            <div style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $isCompleted ? 'var(--pu)' : '#9ca3af' }}; background: {{ $isCompleted ? $stageInfo['color'] : '#f0f0f0' }}; border: 2.5px solid {{ $isCompleted ? ($isCurrent ? 'var(--pk)' : 'var(--mn)') : '#ddd' }}; margin-bottom: 10px; box-shadow: {{ $isCurrent ? '0 0 15px rgba(255, 77, 143, 0.3)' : 'none' }}; transition: all 0.3s; cursor: pointer;" onclick="document.getElementById('returnStatusSelect-{{ $return->id }}').value = '{{ $stage }}'; updateReturnStatus({{ $return->id }}, '{{ $stage }}');" title="Click to set status">
                                                <svg class="return-page-icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                                    @switch($stage)
                                                        @case('pending')<circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />@break
                                                        @case('approved')<circle cx="12" cy="12" r="9" /><path d="m7.5 12.5 3 3 6-7" />@break
                                                        @case('in_transit')<path d="M10 17h4V5H2v12h3" /><path d="M20 17h2v-3.3a4 4 0 0 0-1.2-2.9L19 9h-5" /><circle cx="7.5" cy="17.5" r="2.5" /><circle cx="17.5" cy="17.5" r="2.5" />@break
                                                        @case('received')<path d="m3 7 9-4 9 4-9 4-9-4Z" /><path d="M3 7v10l9 4 9-4V7M12 11v10" />@break
                                                        @case('completed')<rect x="3" y="5" width="18" height="14" rx="2" /><path d="M3 10h18" />@break
                                                    @endswitch
                                                </svg>
                                            </div>
                                            <!-- Stage Label -->
                                            <span style="font-size: 0.75rem; font-weight: {{ $isCompleted ? '800' : '600' }}; color: {{ $isCompleted ? 'var(--dk)' : '#999' }}; text-align: center; max-width: 90px; line-height: 1.3;">
                                                {{ $stageInfo['name'] }}
                                            </span>
                                            <!-- Connector Line -->
                                            @if($index < count($stageOrder) - 1)
                                                <div style="position: absolute; top: 25px; left: 60%; width: 40%; height: 3px; background: {{ $index < $currentIndex ? 'var(--mn)' : '#ddd' }}; z-index: -1;"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <strong style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #aaa; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">
                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z" /></svg> Reason & Comments
                            </strong>
                            <p style="margin: 0; color: var(--dk); font-weight: 700; font-size: 1rem; line-height: 1.5;">{{ $return->reason }}</p>
                        </div>
                        
                        @if($return->admin_note)
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #eee;">
                                <strong style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: var(--mn); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">
                                    <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" /><path d="m9 12 2 2 4-4" /></svg> Admin Response
                                </strong>
                                <p style="margin: 0; color: #444; font-size: 0.95rem; font-weight: 500; font-style: italic; background: #fff; padding: 10px; border-radius: 10px; border-left: 4px solid var(--mn);">{{ $return->admin_note }}</p>
                            </div>
                        @endif
                    </div>

                    @if($return->media_paths && count($return->media_paths) > 0)
                        <div style="margin-top: 20px;">
                            <strong style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #aaa; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">
                                <svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M14 4h-4L8 7H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2-3Z" /><circle cx="12" cy="13" r="3" /></svg> Uploaded Media ({{ count($return->media_paths) }})
                            </strong>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                @foreach($return->media_paths as $path)
                                    @php
                                        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                        $isVideo = in_array($extension, ['mp4', 'mov', 'avi', 'wmv']);
                                    @endphp
                                    <div class="media-preview" style="width: 110px; height: 110px; border-radius: 16px; overflow: hidden; border: 2.5px solid var(--border); position: relative; background: #fafafa;">
                                        @if($isVideo)
                                            <video src="{{ asset('storage/' . $path) }}" style="width: 100%; height: 100%; object-fit: cover;"></video>
                                            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25);">
                                                <svg class="return-page-icon" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9" /><path d="m10 8 6 4-6 4V8Z" /></svg>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/' . $path) }}" alt="Return proof" style="width: 100%; height: 100%; object-fit: cover;" onclick="window.open('{{ asset('storage/' . $path) }}', '_blank')">
                                        @endif
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noopener noreferrer" aria-label="Open return attachment" style="position: absolute; bottom: 8px; right: 8px; background: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.15); border: 1px solid #eee;"><svg class="return-page-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></svg></a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div style="text-align: center; padding: 100px 20px; background: #fff; border-radius: 30px; border: 2.5px dashed var(--border);">
                    <div style="margin-bottom: 30px; color: var(--pu);"><svg class="return-page-icon" viewBox="0 0 24 24" width="72" height="72" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m3 7 9-4 9 4-9 4-9-4Z" /><path d="M3 7v10l9 4 9-4V7M12 11v10" /></svg></div>
                    <h3 style="font-family: 'Fredoka One', cursive; color: var(--dk); font-size: 1.8rem; margin-bottom: 15px;">No return requests yet</h3>
                    <p style="color: #999; font-size: 1.1rem; max-width: 500px; margin: 0 auto 30px; line-height: 1.6;">If you have any issues with your products, you can request a return from your order history within 7 days of delivery.</p>
                    <a href="{{ route('order') }}" style="text-decoration: none; display: inline-flex; align-items: center; gap: 10px; padding: 18px 40px; border-radius: 50px; background: linear-gradient(135deg, var(--pk), var(--pkd)); color: #fff; font-weight: 900; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(255, 77, 143, 0.4); transition: transform 0.2s;">
                        <svg class="return-page-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="9" cy="21" r="1" /><circle cx="20" cy="21" r="1" /><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6" /></svg> View My Orders
                    </a>
                </div>
            @endforelse

            <div style="margin-top: 40px;">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
@endsection

<script>
    function toggleStatusDropdown(button) {
        const dropdown = button.nextElementSibling;
        const isOpen = dropdown.style.display !== 'none';
        
        // Close all dropdowns first
        document.querySelectorAll('.status-dropdown').forEach(d => {
            d.style.display = 'none';
        });
        
        // Toggle current dropdown
        if (!isOpen) {
            dropdown.style.display = 'block';
        }
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.status-dropdown') && !event.target.closest('.status-badge')) {
            document.querySelectorAll('.status-dropdown').forEach(d => {
                d.style.display = 'none';
            });
        }
    });

    // Update return status
    function updateReturnStatus(returnId, newStatus) {
        // Show loading
        nbToast('Updating return status...', 'info');
        
        // Make API request
        fetch(`/user/orders/returns/${returnId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => {
            if (response.ok) {
                nbToast('Return status updated successfully!', 'success');
                // Reload page to reflect changes
                setTimeout(() => location.reload(), 1000);
            } else {
                nbToast('Failed to update status. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            nbToast('An error occurred. Please try again.', 'error');
        });
    }
</script>
