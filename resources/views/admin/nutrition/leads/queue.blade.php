@extends('layouts.admin')
@php($title = $isSuperAdmin ? 'All Nutrition Leads' : 'My Nutrition Leads')
@php($subTitle = $isSuperAdmin ? 'All customers awaiting diet-chart review' : 'Assigned customers awaiting diet-chart review')
@push('styles')<link rel="stylesheet" href="{{ asset('css/nutrition-leads.css') }}">@endpush
@section('content')
<div class="card nutrition-leads-card"><div class="card-body p-24">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Customer</th><th>Child</th><th>Plan</th><th>Assigned</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($leads as $lead)<tr><td><strong>{{ $lead->customer->name }}</strong><br><small>{{ $lead->customer->email }}</small></td><td>{{ $lead->child?->name ?? 'Profile pending' }}</td><td>{{ $lead->subscription->subscriptionPlan?->name ?? $lead->subscription->model }}</td><td>{{ $lead->assigned_at?->format('d M Y') }}</td><td>{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</td><td><a class="btn btn-primary btn-sm" href="{{ route('admin.nutrition.lead-queue.show', $lead) }}">Review chart</a></td></tr>@empty<tr><td colspan="6" class="text-center py-4">{{ $isSuperAdmin ? 'No nutrition leads found.' : 'No leads assigned to you.' }}</td></tr>@endforelse
</tbody></table></div>{{ $leads->links() }}</div></div>
@endsection
