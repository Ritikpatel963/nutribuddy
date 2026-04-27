@extends('layouts.user-panel')
@section('title', 'Personal Info — NutriBuddy Kids')
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
        <span class="it-title">Personal Info 👤</span>
        <div style="width:36px"></div>
    </div>

<!-- EDIT MODAL -->
<div class="modal-backdrop" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Profile ✏️</h3>
      <div class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </div>
    </div>
    <div class="modal-avatar-section">
      <div class="modal-avatar">J</div>
      <button class="modal-avatar-change">📷 Change Photo</button>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">First Name</label>
        <input type="text" class="form-input" placeholder="First name" value="Jaydafsdf">
      </div>
      <div class="form-group">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-input" placeholder="Last name">
      </div>
      <div class="form-group full">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-input" placeholder="Email" value="user@gmail.com">
      </div>
      <div class="form-group">
        <label class="form-label">Phone Number</label>
        <input type="tel" class="form-input" placeholder="+91 00000 00000">
      </div>
      <div class="form-group">
        <label class="form-label">Date of Birth</label>
        <input type="date" class="form-input">
      </div>
      <div class="form-group">
        <label class="form-label">Gender</label>
        <select class="form-input">
          <option value="">Select gender</option>
          <option>Male</option>
          <option>Female</option>
          <option>Other</option>
          <option>Prefer not to say</option>
        </select>
      </div>
      <div class="form-group full">
        <label class="form-label">Bio / About</label>
        <input type="text" class="form-input" placeholder="Tell us a little about yourself...">
      </div>
    </div>
    <div class="modal-btns">
      <button class="m-btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="m-btn-save" onclick="saveProfile()">💾 Save Changes</button>
    </div>
  </div>
</div>

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
    <div class="welcome-banner d1">
      <div class="welcome-text" style="position:relative;z-index:1">
        <h2>Welcome back, <span>Jaydafsdf!</span> 👋</h2>
        <p>Here's a quick overview of your account and profile status.</p>
      </div>
      <div class="welcome-right">
        <div class="banner-stat">
          <div class="bs-num">4</div>
          <div class="bs-lbl">Orders</div>
        </div>
        <div class="banner-stat">
          <div class="bs-num">2026</div>
          <div class="bs-lbl">Member since</div>
        </div>
        <div class="banner-emoji"></div>
      </div>
    </div>

    <!-- PROFILE HERO -->
    <div class="profile-hero fade-in d2">
      <div class="hero-avatar-wrap">
        <div class="hero-avatar">J</div>
        <div class="hero-avatar-ring"></div>
        <div class="avatar-upload" title="Change photo">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
      </div>
      <div class="hero-info">
        <div class="hero-name">
          Jaydafsdf
          <div class="verified">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
        </div>
        <div class="hero-email">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          user@gmail.com
        </div>
        <div class="hero-badges">
          <span class="hero-badge badge-member">⭐ Member since 2026</span>
          <span class="hero-badge badge-orders">📦 4 Orders placed</span>
        </div>
      </div>
      <div class="hero-actions">
        <button class="edit-btn" onclick="openModal()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Profile
        </button>
        <button class="edit-btn secondary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Change Password
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
              <div class="row-value">Jaydafsdf</div>
            </div>
            <span class="row-chip chip-done">✓ Set</span>
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--skl)">📧</div>
            <div class="row-content">
              <div class="row-label">Email Address</div>
              <div class="row-value">user@gmail.com</div>
            </div>
            <span class="row-chip chip-done">✓ Verified</span>
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--yel)">📱</div>
            <div class="row-content">
              <div class="row-label">Phone Number</div>
              <div class="row-value empty">Not provided</div>
            </div>
            <span class="row-chip chip-add" onclick="openModal()">+ Add</span>
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
              <div class="row-value empty">Not provided</div>
            </div>
            <span class="row-chip chip-add" onclick="openModal()">+ Add</span>
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--orl)">🧑</div>
            <div class="row-content">
              <div class="row-label">Gender</div>
              <div class="row-value empty">Not provided</div>
            </div>
            <span class="row-chip chip-add" onclick="openModal()">+ Add</span>
          </div>
          <div class="info-row">
            <div class="row-icon" style="background:var(--mnl)">📝</div>
            <div class="row-content">
              <div class="row-label">Bio</div>
              <div class="row-value empty">Not provided</div>
            </div>
            <span class="row-chip chip-add" onclick="openModal()">+ Add</span>
          </div>
        </div>
      </div>

      <!-- Saved Addresses -->
      <div class="info-section full fade-in d4">
        <div class="section-head">
          <div class="section-title">
            <div class="section-icon" style="background:var(--skl)">📍</div>
            Saved Addresses
          </div>
          <button class="s-edit">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New
          </button>
        </div>
        <div class="address-grid">
          <div class="address-card default">
            <div class="addr-type" style="color:var(--pk)">
              🏠 Home
              <span class="addr-default-tag">Default</span>
            </div>
            <div class="addr-name">Jaydafsdf</div>
            <div class="addr-text">123, Green Lane, Near Park,<br>Bhopal, Madhya Pradesh - 462001<br>📞 +91 98765 43210</div>
            <div class="addr-actions">
              <button class="addr-btn addr-edit">✏️ Edit</button>
              <button class="addr-btn addr-del">🗑 Delete</button>
            </div>
          </div>
          <div class="address-card">
            <div class="addr-type" style="color:var(--pu)">🏢 Work</div>
            <div class="addr-name">Jaydafsdf</div>
            <div class="addr-text">456, Tech Park, Sector 5,<br>Bhopal, Madhya Pradesh - 462011<br>📞 +91 98765 43210</div>
            <div class="addr-actions">
              <button class="addr-btn addr-edit">✏️ Edit</button>
              <button class="addr-btn addr-del">🗑 Delete</button>
            </div>
          </div>
          <div class="add-address">
            <div class="add-icon">➕</div>
            <span>Add New Address</span>
          </div>
        </div>
      </div>

    </div><!-- /info-grid -->

    <!-- DANGER ZONE -->
    <div class="danger-zone fade-in d5">
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

 @push('scripts')
   <script>
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
function openModal(){
  document.getElementById('editModal').classList.add('show');
}
function closeModal(){
  document.getElementById('editModal').classList.remove('show');
}
function saveProfile(){
  closeModal();
  // Could update the displayed values here
}
document.getElementById('editModal').addEventListener('click',function(e){
  if(e.target===this)closeModal();
});
</script>



    @endpush
@endsection