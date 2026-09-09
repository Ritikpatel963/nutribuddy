@extends('layout.layout')
@php($title = 'Nutrition Leads')
@php($subTitle = 'Assign purchased diet plans to nutritionists')
@push('styles')<link rel="stylesheet" href="{{ asset('css/nutrition-leads.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/nutrition-leads.js') }}" defer></script>@endpush

@section('content')
<div class="card nutrition-leads-card">
  <div class="card-body p-24">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <form class="row g-2 mb-4" method="GET">
      <div class="col-md-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search customer name or email"></div>
      <div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option>@foreach(['new','assigned','awaiting_review','completed'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div>
      <div class="col-md-2"><select class="form-select" name="assigned_to"><option value="">All counsellors</option>@foreach($nutritionists as $nutritionist)<option value="{{ $nutritionist->id }}" @selected((string) request('assigned_to') === (string) $nutritionist->id)>{{ $nutritionist->name }}</option>@endforeach</select></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
    </form>
    <form action="{{ route('admin.nutrition.leads.bulk-assign') }}" method="POST" data-bulk-leads>
      @csrf @method('PATCH')
      <div class="d-flex gap-2 align-items-center mb-3">
        <select class="form-select w-auto" name="assigned_to" required><option value="">Assign selected to…</option>@foreach($nutritionists as $nutritionist)<option value="{{ $nutritionist->id }}">{{ $nutritionist->name }}</option>@endforeach</select>
        <button class="btn btn-outline-primary" type="submit">Bulk assign</button>
      </div>
      <div class="table-responsive"><table class="table align-middle"><thead><tr><th><input type="checkbox" data-select-all></th><th>Customer</th><th>Child / Plan</th><th>Purchased</th><th>Status</th><th>Assigned to</th><th>Assign</th></tr></thead><tbody>
      @forelse($leads as $lead)<tr>
        <td><input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" data-lead-checkbox></td>
        <td><strong>{{ $lead->customer->name }}</strong><br><small>{{ $lead->customer->email }}</small></td>
        <td>{{ $lead->child?->name ?? 'Profile pending' }}<br><small>{{ $lead->subscription->subscriptionPlan?->name ?? $lead->subscription->model }}</small></td>
        <td>₹{{ number_format((float) $lead->subscription->amount_paid, 2) }}<br><small>{{ $lead->created_at->format('d M Y') }}</small></td>
        <td><span class="badge bg-{{ $lead->status === 'completed' ? 'success' : 'warning' }}-focus text-{{ $lead->status === 'completed' ? 'success' : 'warning' }}-main">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span></td>
        <td>{{ $lead->assignee?->name ?? 'Unassigned' }}</td>
        <td><select class="form-select form-select-sm" data-assign-url="{{ route('admin.nutrition.leads.assign', $lead) }}"><option value="">Choose…</option>@foreach($nutritionists as $nutritionist)<option value="{{ $nutritionist->id }}" @selected($lead->assigned_to === $nutritionist->id)>{{ $nutritionist->name }}</option>@endforeach</select></td>
      </tr>@empty<tr><td colspan="7" class="text-center py-4">No purchased diet leads found.</td></tr>@endforelse
      </tbody></table></div>
    </form>
    <div class="mt-3">{{ $leads->links() }}</div>
  </div>
</div>
@endsection
