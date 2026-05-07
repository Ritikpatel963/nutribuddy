@extends('layouts.user-panel')
@section('title', 'Add New Address — NutriBuddy Kids')
@section('panel-page-class', 'panel-personal-info')

@section('panel-content')
    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Add New Address 📍</span>
        <div style="width:36px"></div>
    </div>

    <div class="main">
        <div class="page">
            <!-- PAGE HEADER -->
            <div class="page-header fade-in d1">
                <div class="page-header-left">
                    <h1>Add New Address 📍</h1>
                    <p>Where should we deliver your wellness?</p>
                </div>
                <a href="{{ route('personal-info') }}" class="nb-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    Back to Profile
                </a>
            </div>

            <div class="nb-form-container fade-in d2">
                <div style="max-width: 700px;">
                    <div style="margin-bottom:32px;">
                        <label class="nb-form-label">Select Address Type</label>
                        <div style="display:flex;gap:12px;">
                            <button class="nb-type-pill active" data-type="Home" onclick="selectType(this)">🏠 Home</button>
                            <button class="nb-type-pill" data-type="Work" onclick="selectType(this)">💼 Work</button>
                            <button class="nb-type-pill" data-type="Other" onclick="selectType(this)">📍 Other</button>
                        </div>
                    </div>

                    <div class="nb-form-grid" style="gap:32px;">
                        <div class="nb-form-group">
                            <label class="nb-form-label">Full Name *</label>
                            <input type="text" class="nb-form-input" id="addrFullName" placeholder="e.g. Priya Sharma">
                        </div>
                        <div class="nb-form-group">
                            <label class="nb-form-label">Mobile Number *</label>
                            <input type="tel" class="nb-form-input" id="addrPhone" placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div class="nb-form-group nb-full">
                            <label class="nb-form-label">Flat / House / Apartment / Area *</label>
                            <input type="text" class="nb-form-input" id="addrLine1"
                                placeholder="e.g. 42, Sunshine Residency, HSR Layout">
                        </div>
                        <div class="nb-form-group nb-full">
                            <label class="nb-form-label">Landmark (Optional)</label>
                            <input type="text" class="nb-form-input" id="addrLandmark"
                                placeholder="Near park, opposite school…">
                        </div>
                        <div class="nb-form-group">
                            <label class="nb-form-label">Pincode *</label>
                            <input type="text" class="nb-form-input" id="addrPincode" maxlength="6"
                                placeholder="6-digit pincode">
                        </div>
                        <div class="nb-form-group">
                            <label class="nb-form-label">City *</label>
                            <input type="text" class="nb-form-input" id="addrCity" placeholder="City">
                        </div>
                        <div class="nb-form-group nb-full">
                            <label class="nb-form-label">State *</label>
                            <select class="nb-form-input" id="addrState">
                                <option value="">Select State</option>
                                <option>Andhra Pradesh</option>
                                <option>Assam</option>
                                <option>Bihar</option>
                                <option>Delhi</option>
                                <option>Goa</option>
                                <option>Gujarat</option>
                                <option>Haryana</option>
                                <option>Himachal Pradesh</option>
                                <option>Jharkhand</option>
                                <option>Karnataka</option>
                                <option>Kerala</option>
                                <option>Madhya Pradesh</option>
                                <option>Maharashtra</option>
                                <option>Odisha</option>
                                <option>Punjab</option>
                                <option>Rajasthan</option>
                                <option>Tamil Nadu</option>
                                <option>Telangana</option>
                                <option>Uttar Pradesh</option>
                                <option>Uttarakhand</option>
                                <option>West Bengal</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:40px;display:flex;gap:15px;max-width: 450px;">
                        <a href="{{ route('personal-info') }}" class="nb-btn-cancel">Cancel</a>
                        <button class="nb-btn-save" id="addrSaveBtn" onclick="saveNewAddress()">💾 Save Address</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .nb-back-link {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 800;
                font-size: 14px;
                color: var(--pk);
                text-decoration: none;
                transition: all 0.2s;
                background: var(--pkl);
                padding: 10px 18px;
                border-radius: 12px;
            }

            .nb-back-link:hover {
                transform: translateX(-4px);
                background: var(--pk);
                color: #fff;
            }

            .nb-form-container {
                background: #fff !important;
                border: 1px solid #edf2f7 !important;
                border-radius: 35px !important;
                padding: 50px !important;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.03) !important;
            }

            .nb-form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 32px;
            }

            .nb-form-group {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
                margin-bottom: 24px !important;
            }

            .nb-form-group.nb-full {
                grid-column: 1 / -1;
            }

            .nb-type-pill {
                border: 2px solid #f1f5f9;
                border-radius: 50px;
                padding: 12px 24px;
                font-family: 'Nunito', sans-serif;
                font-weight: 800;
                font-size: .88rem;
                cursor: pointer;
                background: #fff;
                color: #64748b;
                transition: all .25s;
            }

            .nb-type-pill:hover {
                border-color: #e2e8f0;
                background: #f8fafc;
            }

            .nb-type-pill.active {
                border-color: var(--pk);
                background: var(--pkl);
                color: var(--pkd);
            }

            .nb-form-label {
                display: block !important;
                margin-bottom: 10px !important;
                font-weight: 800 !important;
                color: #1e293b !important;
                font-size: 0.85rem !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
            }

            .nb-form-input {
                width: 100% !important;
                border: 2px solid #f1f5f9 !important;
                background: #f8fafc !important;
                border-radius: 16px !important;
                padding: 14px 18px !important;
                font-size: 15px !important;
                font-weight: 600 !important;
                transition: all 0.2s !important;
                outline: none !important;
                font-family: 'Nunito', sans-serif !important;
            }

            .nb-form-input:focus {
                border-color: var(--pk) !important;
                background: #fff !important;
                box-shadow: 0 0 0 4px var(--pkl) !important;
            }

            .nb-btn-save {
                flex: 2;
                height: 54px;
                border-radius: 18px;
                font-weight: 900;
                cursor: pointer;
                background: linear-gradient(135deg, var(--pk), var(--pkd)) !important;
                color: white !important;
                border: none !important;
                box-shadow: 0 8px 20px rgba(255, 77, 143, 0.3) !important;
                transition: all 0.3s !important;
                font-family: 'Nunito', sans-serif !important;
            }

            .nb-btn-save:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 12px 28px rgba(255, 77, 143, 0.45) !important;
            }

            .nb-btn-cancel {
                flex: 1;
                height: 54px;
                border-radius: 18px;
                font-weight: 800;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                background: #f1f5f9;
                color: #475569;
                transition: all 0.2s;
                font-family: 'Nunito', sans-serif !important;
            }

            .nb-btn-cancel:hover {
                background: #e2e8f0;
                color: #1e293b;
            }

            @media(max-width: 640px) {
                .nb-form-grid { grid-template-columns: 1fr; }
                .nb-form-container { padding: 30px !important; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const _addrStoreUrl = '{{ route("user.addresses.store") }}';
            const _csrf = '{{ csrf_token() }}';
            let _selectedType = 'Home';

            function selectType(btn) {
                document.querySelectorAll('.nb-type-pill').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                _selectedType = btn.dataset.type;
            }

            async function saveNewAddress() {
                const btn = document.getElementById('addrSaveBtn');
                const fields = {
                    full_name: document.getElementById('addrFullName').value.trim(),
                    phone: document.getElementById('addrPhone').value.trim(),
                    address_line_1: document.getElementById('addrLine1').value.trim(),
                    landmark: document.getElementById('addrLandmark').value.trim(),
                    postal_code: document.getElementById('addrPincode').value.trim(),
                    city: document.getElementById('addrCity').value.trim(),
                    state: document.getElementById('addrState').value
                };

                if (!fields.full_name || !fields.phone || !fields.address_line_1 || !fields.postal_code || !fields.city || !fields.state) {
                    nbToast('Please fill all required fields.', 'warning');
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Saving…';

                try {
                    const res = await fetch(_addrStoreUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ label: _selectedType, ...fields })
                    });
                    const json = await res.json();
                    if (!res.ok) { throw new Error(Object.values(json.errors || {}).flat().join(' ') || json.message || 'Error saving.'); }

                    nbToast('Address saved successfully!', 'success');
                    setTimeout(() => window.location.href = '{{ route("personal-info") }}', 800);
                } catch (err) {
                    nbToast(err.message || 'Could not save address.', 'error');
                    btn.disabled = false;
                    btn.textContent = '💾 Save Address';
                }
            }
        </script>
    @endpush
@endsection