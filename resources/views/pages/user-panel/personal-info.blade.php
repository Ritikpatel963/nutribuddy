@extends('layouts.user-panel')
@section('title', 'Personal Info — NutriBuddy Kids')
@section('panel-page-class', 'panel-personal-info')

@section('panel-content')

@php
    $user = auth()->user();
    $avatar = $user->avatar ? asset('storage/'.$user->avatar) : null;
    $initial = strtoupper(substr($user->name, 0, 1));
@endphp

    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Personal Info 👤</span>
        <div style="width:36px"></div>
    </div>

<!-- EDIT MODAL moved to bottom for z-index fix -->

<!-- ════════ MAIN ════════ -->
<div class="main">
 

  <div class="page">

    <!-- PAGE HEADER -->
    <div class="page-header fade-in d1">
      <div class="page-header-left">
        <h1>Personal Info 👤</h1>
        <p>Manage your profile details and account settings</p>
      </div>
    </div>

    <!-- WELCOME BANNER -->
    <!-- <div class="welcome-banner d1">
      <div class="welcome-text" style="position:relative;z-index:1">
        <h2>Welcome back, <span>{{ explode(' ', $user->name)[0] ?? '' }}!</span> 👋</h2>
        <p>Here's a quick overview of your account and profile status.</p>
      </div>
      <div class="welcome-right">
        <div class="banner-stat">
          <div class="bs-num">{{ $user->orders()->count() }}</div>
          <div class="bs-lbl">Orders</div>
        </div>
        <div class="banner-stat">
          <div class="bs-num">{{ $user->created_at->format('Y') }}</div>
          <div class="bs-lbl">Member since</div>
        </div>
        <div class="banner-emoji"></div>
      </div>
    </div> -->

    <!-- PROFILE HERO -->
    <div class="profile-hero fade-in d2">
      <div class="hero-avatar-wrap">
        <input type="file" id="heroAvatarInput" accept="image/*" style="display: none;">
        <div class="hero-avatar" id="heroAvatarPreview">
            @if($avatar)
                <img src="{{ $avatar }}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
            @else
                {{ $initial }}
            @endif
        </div>
        <div class="hero-avatar-ring"></div>
        <div class="avatar-upload" title="Change photo" onclick="document.getElementById('heroAvatarInput').click()">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
      </div>
      <div class="hero-info">
        <div class="hero-name">
          {{ $user->name }}
          <div class="verified">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
        </div>
        <div class="hero-email">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          {{ $user->email }}
        </div>
        <div class="hero-badges">
          <span class="hero-badge badge-member">⭐ Member since {{ $user->created_at->format('Y') }}</span>
          <span class="hero-badge badge-orders">📦 {{ $user->orders()->count() }} Orders placed</span>
        </div>
      </div>
      <div class="hero-actions">
        <button class="edit-btn" onclick="openModal()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Profile
        </button>
      </div>
    </div>

    <!-- INFO GRID -->
    <div class="info-grid">

      <!-- Basic Details -->
      <div class="info-section fade-in d3">
        <div class="section-head">
          <div class="section-title">
            <div class="section-icon" style="background:var(--pkl)">👤</div>
            Basic Details
          </div>
          <button class="s-edit" onclick="openModal()">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
          </button>
        </div>
        <div class="info-list">
          <div class="info-row">
            <div class="row-icon" style="background:var(--pkl)">🪪</div>
            <div class="row-content">
              <div class="row-label">Full Name</div>
              <div class="row-value">{{ $user->name }}</div>
            </div>
            <span class="row-chip chip-done">✓ Set</span>
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--skl)">📧</div>
            <div class="row-content">
              <div class="row-label">Email Address</div>
              <div class="row-value">{{ $user->email }}</div>
            </div>
            <span class="row-chip chip-done">✓ Verified</span>
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--yel)">📱</div>
            <div class="row-content">
              <div class="row-label">Phone Number</div>
              @if($user->phone)
                <div class="row-value">{{ $user->phone }}</div>
              @else
                <div class="row-value empty">Not provided</div>
              @endif
            </div>
            @if($user->phone)
                <span class="row-chip chip-done">✓ Set</span>
            @else
                <span class="row-chip chip-add" onclick="openModal()">+ Add</span>
            @endif
          </div>
        </div>
      </div>

      <!-- Personal Details -->
      <div class="info-section fade-in d3">
        <div class="section-head">
          <div class="section-title">
            <div class="section-icon" style="background:var(--pul)">🎂</div>
            Personal Details
          </div>
          <button class="s-edit" onclick="openModal()">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
          </button>
        </div>
        <div class="info-list">
          <div class="info-row">
            <div class="row-icon" style="background:var(--pul)">🎂</div>
            <div class="row-content">
              <div class="row-label">Date of Birth</div>
              @if($user->dob)
                <div class="row-value">{{ \Carbon\Carbon::parse($user->dob)->format('d M, Y') }}</div>
              @else
                <div class="row-value empty">Not provided</div>
              @endif
            </div>
            @if(!$user->dob) <span class="row-chip chip-add" onclick="openModal()">+ Add</span> @else <span class="row-chip chip-done">✓ Set</span> @endif
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--orl)">🧑</div>
            <div class="row-content">
              <div class="row-label">Gender</div>
              @if($user->gender)
                <div class="row-value">{{ $user->gender }}</div>
              @else
                <div class="row-value empty">Not provided</div>
              @endif
            </div>
            @if(!$user->gender) <span class="row-chip chip-add" onclick="openModal()">+ Add</span> @else <span class="row-chip chip-done">✓ Set</span> @endif
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--mnl)">📝</div>
            <div class="row-content">
              <div class="row-label">Bio</div>
              @if($user->bio)
                <div class="row-value">{{ $user->bio }}</div>
              @else
                <div class="row-value empty">Not provided</div>
              @endif
            </div>
            @if(!$user->bio) <span class="row-chip chip-add" onclick="openModal()">+ Add</span> @else <span class="row-chip chip-done">✓ Set</span> @endif
          </div>
        </div>
      </div>

      <!-- Saved Addresses -->
      <div class="info-section full fade-in d4" id="addressSection">
        <div class="section-head">
          <div class="section-title">
            <div class="section-icon" style="background:var(--skl)">📍</div>
            Saved Addresses
          </div>
        </div>
        <div class="address-grid" id="addressGrid">
          @forelse($savedAddresses as $addr)
          <div class="address-card {{ $loop->first ? 'default' : '' }}" id="addr-card-{{ $addr->id }}">
            <div class="addr-type" style="color:{{ $loop->first ? 'var(--pk)' : 'var(--pu)' }}">
              {{ $addr->label === 'Work' ? '🏢' : ($addr->label === 'Other' ? '📍' : '🏠') }} {{ $addr->label ?? 'Home' }}
              @if($loop->first)<span class="addr-default-tag">Default</span>@endif
            </div>
            <div class="addr-name">{{ $addr->full_name }}</div>
            <div class="addr-text">
              {{ $addr->address_line_1 }}{{ $addr->address_line_2 ? ', '.$addr->address_line_2 : '' }}{{ $addr->landmark ? ', Near '.$addr->landmark : '' }}<br>
              {{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}<br>
              📞 {{ $addr->phone }}
            </div>
            <div class="addr-actions">
              <button class="addr-btn addr-del" onclick="deleteAddress({{ $addr->id }}, this)">🗑 Delete</button>
            </div>
          </div>
          @empty
          <div class="no-addr-msg" id="noAddrMsg">
            <div style="font-size:2.5rem;margin-bottom:8px">📭</div>
            <div style="font-weight:700;color:var(--dk);margin-bottom:4px">No saved addresses yet</div>
            <div style="font-size:.82rem;color:var(--text-light)">You don't have any saved addresses.</div>
          </div>
          @endforelse
        </div>
      </div>

    </div><!-- /info-grid -->

    <!-- DANGER ZONE -->
    <div class="danger-zone fade-in d5" style="display:none;">
      <div class="danger-head">
        <div class="d-icon">⚠️</div>
        <h3>Danger Zone</h3>
      </div>
      <div class="danger-items">
        <div class="danger-row">
          <div class="danger-row-info">
            <h4>Deactivate Account</h4>
            <p>Temporarily disable your account. You can reactivate anytime.</p>
          </div>
          <button class="danger-btn d-btn-soft">Deactivate</button>
        </div>
        <div class="danger-row">
          <div class="danger-row-info">
            <h4>Delete Account</h4>
            <p>Permanently delete your account and all associated data. This cannot be undone.</p>
          </div>
          <button class="danger-btn d-btn-hard">Delete Account</button>
        </div>
      </div>
    </div>

  </div><!-- /page -->
</div><!-- /main -->

<!-- ── EDIT PROFILE MODAL ── -->
<div class="modal-backdrop" id="editModal" style="display:none;align-items:center;justify-content:center;">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Profile ✏️</h3>
      <div class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </div>
    </div>
    <div class="modal-avatar-section">
      <input type="file" id="modalAvatarInput" accept="image/*" style="display: none;">
      <div class="modal-avatar" id="modalAvatarPreview">
        @if($avatar)
            <img src="{{ $avatar }}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
        @else
            {{ $initial }}
        @endif
      </div>
      <button class="modal-avatar-change" onclick="document.getElementById('modalAvatarInput').click()">📷 Change Photo</button>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">First Name</label>
        <input type="text" class="form-input" id="editFirstName" placeholder="First name" value="{{ explode(' ', $user->name)[0] ?? '' }}">
      </div>
      <div class="form-group">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-input" id="editLastName" placeholder="Last name" value="{{ implode(' ', array_slice(explode(' ', $user->name), 1)) }}">
      </div>
      <div class="form-group full">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-input" placeholder="Email" value="{{ $user->email }}" readonly style="opacity:0.7;cursor:not-allowed">
      </div>
      <div class="form-group">
        <label class="form-label">Phone Number</label>
        <input type="tel" class="form-input" id="editPhone" placeholder="+91 00000 00000" value="{{ $user->phone }}">
      </div>
      <div class="form-group">
        <label class="form-label">Date of Birth</label>
        <input type="date" class="form-input" id="editDob" value="{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '' }}">
      </div>
      <div class="form-group">
        <label class="form-label">Gender</label>
        <select class="form-input" id="editGender">
          <option value="">Select gender</option>
          <option {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
          <option {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
          <option {{ $user->gender == 'Other' ? 'selected' : '' }}>Other</option>
          <option {{ $user->gender == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
        </select>
      </div>
      <div class="form-group full">
        <label class="form-label">Bio / About</label>
        <input type="text" class="form-input" id="editBio" placeholder="Tell us a little about yourself..." value="{{ $user->bio }}">
      </div>
    </div>
    <div class="modal-btns">
      <button class="m-btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="m-btn-save" id="btnSaveProfile" onclick="saveProfile()">💾 Save Changes</button>
    </div>
  </div>
</div>

{{-- ── ADD ADDRESS MODAL ── --}}
<div class="modal-backdrop" id="addressModal" style="display:none;align-items:center;justify-content:center;">
  <div class="modal" style="max-width:560px;width:95%;max-height:90vh;overflow-y:auto;">
    <div class="modal-header">
      <h3>📍 Add New Address</h3>
      <div class="modal-close" onclick="closeAddressModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </div>
    </div>
    <div style="padding:24px;">

      {{-- Address Type --}}
      <div style="margin-bottom:18px;">
        <label class="form-label" style="margin-bottom:10px;display:block;">Address Type</label>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button class="addr-type-pill active" data-type="Home" onclick="selectType(this)">🏠 Home</button>
          <button class="addr-type-pill" data-type="Work" onclick="selectType(this)">💼 Work</button>
          <button class="addr-type-pill" data-type="Other" onclick="selectType(this)">📍 Other</button>
        </div>
      </div>

      <div class="form-grid" style="gap:14px;">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" class="form-input" id="addrFullName" placeholder="e.g. Priya Sharma">
        </div>
        <div class="form-group">
          <label class="form-label">Mobile Number *</label>
          <input type="tel" class="form-input" id="addrPhone" placeholder="+91 XXXXX XXXXX">
        </div>
        <div class="form-group full">
          <label class="form-label">Flat / House / Apartment *</label>
          <input type="text" class="form-input" id="addrLine1" placeholder="e.g. 42, Sunshine Residency">
        </div>
        <div class="form-group full">
          <label class="form-label">Street / Area / Colony</label>
          <input type="text" class="form-input" id="addrLine2" placeholder="e.g. HSR Layout, Sector 3">
        </div>
        <div class="form-group full">
          <label class="form-label">Landmark</label>
          <input type="text" class="form-input" id="addrLandmark" placeholder="Near park, opposite school…">
        </div>
        <div class="form-group">
          <label class="form-label">Pincode *</label>
          <input type="text" class="form-input" id="addrPincode" maxlength="6" placeholder="6-digit pincode">
        </div>
        <div class="form-group">
          <label class="form-label">City *</label>
          <input type="text" class="form-input" id="addrCity" placeholder="City">
        </div>
        <div class="form-group full">
          <label class="form-label">State *</label>
          <select class="form-input" id="addrState">
            <option value="">Select State</option>
            <option>Andhra Pradesh</option><option>Assam</option><option>Bihar</option>
            <option>Delhi</option><option>Goa</option><option>Gujarat</option>
            <option>Haryana</option><option>Himachal Pradesh</option><option>Jharkhand</option>
            <option>Karnataka</option><option>Kerala</option><option>Madhya Pradesh</option>
            <option>Maharashtra</option><option>Odisha</option><option>Punjab</option>
            <option>Rajasthan</option><option>Tamil Nadu</option><option>Telangana</option>
            <option>Uttar Pradesh</option><option>Uttarakhand</option><option>West Bengal</option>
          </select>
        </div>
      </div>



      <div class="modal-btns" style="margin-top:22px;">
        <button class="m-btn-cancel" onclick="closeAddressModal()">Cancel</button>
        <button class="m-btn-save" id="addrSaveBtn" onclick="saveNewAddress()">💾 Save Address</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.addr-type-pill {
  border: 2px solid var(--border);
  border-radius: 50px;
  padding: 8px 16px;
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  font-size: .8rem;
  cursor: pointer;
  background: white;
  color: var(--text-light);
  transition: all .25s;
}
.addr-type-pill.active {
  border-color: var(--pk);
  background: var(--pkl);
  color: var(--pkd);
}
.address-card {
  background: white;
  border: 2px solid var(--border);
  border-radius: 16px;
  padding: 18px;
  position: relative;
  transition: all .3s;
}
.address-card.default {
  border-color: var(--pk);
  background: linear-gradient(135deg, rgba(255,77,143,.04), white);
}
.address-card:hover {
  box-shadow: 0 6px 20px rgba(0,0,0,.08);
  transform: translateY(-2px);
}
.addr-default-tag {
  background: var(--pkl);
  color: var(--pkd);
  font-size: .62rem;
  font-weight: 900;
  padding: 2px 8px;
  border-radius: 20px;
  font-family: 'Nunito', sans-serif;
  margin-left: 6px;
}
.addr-name { font-family:'Nunito',sans-serif; font-weight:900; font-size:.95rem; color:var(--dk); margin:6px 0 4px; }
.addr-text { font-size:.83rem; color:var(--text-light); line-height:1.6; }
.addr-actions { margin-top:12px; display:flex; gap:8px; }
.addr-btn { border:none; border-radius:10px; padding:7px 14px; font-family:'Nunito',sans-serif; font-weight:800; font-size:.78rem; cursor:pointer; transition:all .2s; }
.addr-del { background: #fff0f0; color: #e74c3c; }
.addr-del:hover { background:#e74c3c; color:white; }
.add-address {
  border: 2.5px dashed var(--pkl);
  border-radius: 16px;
  padding: 32px 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  color: var(--pk);
  font-family: 'Nunito', sans-serif;
  font-weight: 900;
  font-size: .9rem;
  transition: all .25s;
}
.add-address:hover { background: var(--pkl); border-style:solid; }
.add-icon { font-size: 1.6rem; }
.no-addr-msg {
  text-align: center;
  padding: 28px 18px;
  border: 2px dashed var(--border);
  border-radius: 16px;
  color: var(--text-light);
  grid-column: 1 / -1;
}
#addressModal.show,
#editModal.show {
  display: flex !important;
}
</style>
@endpush

@push('scripts')
<script>
const _addrStoreUrl  = '{{ route("user.addresses.store") }}';
const _addrDeleteBase = '{{ url("/user/addresses") }}';
const _csrf = '{{ csrf_token() }}';

// ── Sidebar helpers ──
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('show');
}
function setActive(el){
  document.querySelectorAll('.nav-item').forEach(i=>i.classList.remove('active'));
  el.classList.add('active');
}

// ── Profile Edit Modal ──
function openModal(){ 
    try {
        const m = document.getElementById('editModal');
        if (m) {
            m.style.setProperty('display', 'flex', 'important');
            m.classList.add('show');
        } else {
            console.error('editModal element not found in DOM!');
        }
    } catch(e) { console.error('Error opening modal:', e); }
}
function closeModal(){ 
    const m = document.getElementById('editModal');
    if (m) {
        m.style.setProperty('display', 'none', 'important');
        m.classList.remove('show');
    }
}
function saveProfile(){
    const btn = document.getElementById('btnSaveProfile');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const formData = new FormData();
    formData.append('first_name', document.getElementById('editFirstName').value.trim());
    formData.append('last_name', document.getElementById('editLastName').value.trim());
    formData.append('phone', document.getElementById('editPhone').value.trim());
    formData.append('dob', document.getElementById('editDob').value);
    formData.append('gender', document.getElementById('editGender').value);
    formData.append('bio', document.getElementById('editBio').value.trim());

    const avatarInput = document.getElementById('modalAvatarInput');
    if (avatarInput && avatarInput.files[0]) {
        formData.append('avatar', avatarInput.files[0]);
    } else {
        const heroInput = document.getElementById('heroAvatarInput');
        if (heroInput && heroInput.files[0]) {
            formData.append('avatar', heroInput.files[0]);
        }
    }

    fetch('{{ route("personal-info.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': _csrf,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            nbToast('Profile updated successfully!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            nbToast(data.message || 'Error saving profile', 'error');
            btn.disabled = false;
            btn.textContent = '💾 Save Changes';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        nbToast('An unexpected error occurred.', 'error');
        btn.disabled = false;
        btn.textContent = '💾 Save Changes';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('click', function(e){ 
            if(e.target === this) closeModal(); 
        });
    }

    // Avatar preview logic
    function handleAvatarSelect(e, previewId) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewEl = document.getElementById(previewId);
                if (previewEl) {
                    previewEl.innerHTML = `<img src="${evt.target.result}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    const heroInput = document.getElementById('heroAvatarInput');
    if (heroInput) heroInput.addEventListener('change', (e) => handleAvatarSelect(e, 'heroAvatarPreview'));

    const modalInput = document.getElementById('modalAvatarInput');
    if (modalInput) modalInput.addEventListener('change', (e) => handleAvatarSelect(e, 'modalAvatarPreview'));
});

// ── Address Type Pill ──
let _selectedType = 'Home';
function selectType(btn){
  document.querySelectorAll('.addr-type-pill').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  _selectedType = btn.dataset.type;
}

// ── Open / Close Address Modal ──
function openAddressModal(){
  document.getElementById('addrSaveError').style.display = 'none';
  document.getElementById('addressModal').classList.add('show');
  document.getElementById('addressModal').style.display = 'flex';
}
function closeAddressModal(){
  document.getElementById('addressModal').classList.remove('show');
  document.getElementById('addressModal').style.display = 'none';
  ['addrFullName','addrPhone','addrLine1','addrLine2','addrLandmark','addrPincode','addrCity','addrState']
    .forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
  _selectedType = 'Home';
  document.querySelectorAll('.addr-type-pill').forEach((b,i)=>b.classList.toggle('active',i===0));
}
document.getElementById('addressModal').addEventListener('click',function(e){ if(e.target===this)closeAddressModal(); });

// ── Save New Address ──
async function saveNewAddress(){
  const btn = document.getElementById('addrSaveBtn');

  const full_name    = document.getElementById('addrFullName').value.trim();
  const phone        = document.getElementById('addrPhone').value.trim();
  const address_line_1 = document.getElementById('addrLine1').value.trim();
  const address_line_2 = document.getElementById('addrLine2').value.trim();
  const landmark     = document.getElementById('addrLandmark').value.trim();
  const postal_code  = document.getElementById('addrPincode').value.trim();
  const city         = document.getElementById('addrCity').value.trim();
  const state        = document.getElementById('addrState').value;

  if(!full_name||!phone||!address_line_1||!postal_code||!city||!state){
    nbToast('Please fill all required fields.', 'warning');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Saving…';

  try {
    const res = await fetch(_addrStoreUrl, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': _csrf, 'Accept':'application/json' },
      body: JSON.stringify({ label: _selectedType, full_name, phone, address_line_1, address_line_2, landmark, postal_code, city, state })
    });
    const json = await res.json();
    if(!res.ok){ throw new Error(Object.values(json.errors||{}).flat().join(' ') || json.message || 'Error saving.'); }

    // Inject new card into grid
    const addr = json.data;
    const grid = document.getElementById('addressGrid');
    const noMsg = document.getElementById('noAddrMsg');
    if(noMsg) noMsg.remove();

    const typeIcon = addr.label==='Work'?'🏢':(addr.label==='Other'?'📍':'🏠');
    const isFirst  = grid.querySelectorAll('.address-card').length === 0;

    const card = document.createElement('div');
    card.id = 'addr-card-'+addr.id;
    card.className = 'address-card' + (isFirst?' default':'');
    card.innerHTML = `
      <div class="addr-type" style="color:${isFirst?'var(--pk)':'var(--pu)'}">
        ${typeIcon} ${addr.label||'Home'}
        ${isFirst?'<span class="addr-default-tag">Default</span>':''}
      </div>
      <div class="addr-name">${addr.full_name}</div>
      <div class="addr-text">
        ${addr.address_line_1}${addr.address_line_2?', '+addr.address_line_2:''}${addr.landmark?', Near '+addr.landmark:''}<br>
        ${addr.city}, ${addr.state} - ${addr.postal_code}<br>
        📞 ${addr.phone}
      </div>
      <div class="addr-actions">
        <button class="addr-btn addr-del" onclick="deleteAddress(${addr.id}, this)">🗑 Delete</button>
      </div>`;

    // Insert before the last "add" tile
    const addTile = grid.querySelector('.add-address');
    grid.insertBefore(card, addTile);

    closeAddressModal();
    nbToast('Address saved successfully!', 'success');
  } catch(err){
    nbToast(err.message || 'Could not save address.', 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = '💾 Save Address';
  }
}

// ── Delete Address ──
function deleteAddress(id, btn){
  nbConfirm('This address will be permanently removed.', async () => {
    btn.disabled = true;
    try {
      const res = await fetch(_addrDeleteBase+'/'+id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' }
      });
      if(!res.ok) throw new Error('Delete failed');
      const card = document.getElementById('addr-card-'+id);
      if(card){
        card.style.transition = 'opacity .3s, transform .3s';
        card.style.opacity = '0'; card.style.transform = 'scale(.95)';
        setTimeout(()=>{ card.remove(); checkEmpty(); }, 300);
      }
      nbToast('Address deleted successfully.', 'success');
    } catch(e){
      nbToast('Could not delete address. Please try again.', 'error');
      btn.disabled = false;
    }
  }, { title: 'Delete Address?', okText: 'Yes, Delete' });
}

function checkEmpty(){
  const grid = document.getElementById('addressGrid');
  if(!grid) return;
  const cards = grid.querySelectorAll('.address-card');
  if(cards.length === 0 && !grid.querySelector('.no-addr-msg')){
    const msg = document.createElement('div');
    msg.id = 'noAddrMsg';
    msg.className = 'no-addr-msg';
    msg.innerHTML = '<div style="font-size:2.5rem;margin-bottom:8px">📭</div><div style="font-weight:700;color:var(--dk);margin-bottom:4px">No saved addresses yet</div><div style="font-size:.82rem;color:var(--text-light)">You don\'t have any saved addresses.</div>';
    grid.appendChild(msg);
  }
}
</script>
@endpush
@endsection