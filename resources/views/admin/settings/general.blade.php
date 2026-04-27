@extends('layout.layout')
@php
    $title = 'General Settings';
    $subTitle = 'Manage site-wide configurations and SEO defaults';
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Site Configuration</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ecommerce.settings.general.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Site Name</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="solar:global-outline"></iconify-icon>
                                </span>
                                <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="form-control" placeholder="Enter Site Name">
                            </div>
                            @error('site_name')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mage:email"></iconify-icon>
                                </span>
                                <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="form-control" placeholder="admin@example.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact Phone</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="solar:phone-outline"></iconify-icon>
                                </span>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" class="form-control" placeholder="+91 XXXX XXXX">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Office Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full address...">{{ old('address', $settings['address']) }}</textarea>
                        </div>

                        <hr class="my-4">

                        <div class="col-12">
                            <h6 class="mb-3">Global SEO Defaults</h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Default Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" placeholder="This shows when a page doesn't have its own description">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                            <p class="text-xs text-secondary-light mt-8">Recommended: 150-160 characters. Used for Home, Contact, and other generic pages.</p>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Default Meta Keywords</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="lucide:hash"></iconify-icon>
                                </span>
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords']) }}" class="form-control" placeholder="keyword1, keyword2, keyword3">
                            </div>
                            <p class="text-xs text-secondary-light mt-8">Separated by commas.</p>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary-600 px-32">Save All Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
