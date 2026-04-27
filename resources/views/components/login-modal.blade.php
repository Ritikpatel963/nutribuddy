<div id="nbLoginModal" class="nb-modal-overlay">
    <div class="nb-modal-content">
        <button type="button" class="nb-modal-close" onclick="closeLoginModal()">&times;</button>
        
        <div class="nb-modal-header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="NutriBuddy" class="nb-modal-logo">
            <h2 id="loginModalTitle">Welcome Back! 👋</h2>
            <p id="loginModalSub">Login or sign up to continue your healthy journey.</p>
        </div>

        <div class="nb-modal-body">
            <!-- Step 1: Phone -->
            <div id="loginStepPhone" class="login-step active">
                <div class="input-group">
                    <label for="loginPhone">Phone Number</label>
                    <div class="phone-input-wrapper">
                        <span class="prefix">+91</span>
                        <input type="tel" id="loginPhone" placeholder="Enter 10-digit number" maxlength="10">
                    </div>
                </div>
                <button type="button" class="nb-btn-primary w-100 mt-4" onclick="handleSendOtp()">
                    <span class="btn-text">Send OTP</span>
                    <span class="btn-loader d-none"></span>
                </button>
            </div>

            <!-- Step 2: OTP -->
            <div id="loginStepOtp" class="login-step">
                <div class="input-group">
                    <label>Verify OTP</label>
                    <p class="text-sm text-muted mb-3">Sent to <span id="displayPhone"></span></p>
                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-field" data-index="0">
                        <input type="text" maxlength="1" class="otp-field" data-index="1">
                        <input type="text" maxlength="1" class="otp-field" data-index="2">
                        <input type="text" maxlength="1" class="otp-field" data-index="3">
                        <input type="text" maxlength="1" class="otp-field" data-index="4">
                        <input type="text" maxlength="1" class="otp-field" data-index="5">
                    </div>
                </div>
                <button type="button" class="nb-btn-primary w-100 mt-4" onclick="handleVerifyOtp()">
                    <span class="btn-text">Verify & Login</span>
                    <span class="btn-loader d-none"></span>
                </button>
                <div class="mt-4 text-center">
                    <p class="text-sm text-muted">Didn't receive code? <button type="button" id="resendBtn" class="nb-link-btn" disabled onclick="handleSendOtp()">Resend in <span id="timer">30</span>s</button></p>
                </div>
            </div>
        </div>

        <div id="loginError" class="nb-alert nb-alert-danger d-none"></div>
    </div>
</div>

<style>
    .nb-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .nb-modal-overlay.show {
        display: flex;
        opacity: 1;
    }

    .nb-modal-content {
        background: #fff;
        width: 90%;
        max-width: 420px;
        border-radius: 32px;
        padding: 40px;
        position: relative;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .nb-modal-overlay.show .nb-modal-content {
        transform: scale(1);
    }

    .nb-modal-close {
        position: absolute;
        top: 20px;
        right: 24px;
        background: #f8f9fa;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .nb-modal-close:hover {
        background: #eee;
        color: #333;
    }

    .nb-modal-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .nb-modal-logo {
        height: 45px;
        margin-bottom: 20px;
    }

    .nb-modal-header h2 {
        font-family: 'Fredoka One', cursive;
        color: #333;
        font-size: 1.8rem;
        margin-bottom: 8px;
    }

    .nb-modal-header p {
        color: #777;
        font-size: 0.95rem;
    }

    .input-group label {
        display: block;
        font-weight: 700;
        color: #444;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .phone-input-wrapper {
        display: flex;
        align-items: center;
        border: 2px solid #eee;
        border-radius: 16px;
        padding: 4px 16px;
        transition: border-color 0.3s;
    }

    .phone-input-wrapper:focus-within {
        border-color: var(--pk);
    }

    .phone-input-wrapper .prefix {
        font-weight: 700;
        color: #999;
        margin-right: 12px;
        border-right: 1px solid #eee;
        padding-right: 12px;
    }

    .phone-input-wrapper input {
        border: none;
        outline: none;
        width: 100%;
        padding: 12px 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        letter-spacing: 1px;
    }

    .nb-btn-primary {
        background: linear-gradient(135deg, var(--pk) 0%, #ff4d8d 100%);
        color: #fff;
        border: none;
        border-radius: 16px;
        padding: 16px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 10px 20px -5px rgba(255, 107, 156, 0.4);
    }

    .nb-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(255, 107, 156, 0.5);
    }

    .nb-btn-primary:active {
        transform: translateY(0);
    }

    .login-step {
        display: none;
    }

    .login-step.active {
        display: block;
        animation: slideIn 0.4s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: space-between;
    }

    .otp-field {
        width: 45px;
        height: 55px;
        border: 2px solid #eee;
        border-radius: 12px;
        text-align: center;
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--pk);
        transition: all 0.2s;
    }

    .otp-field:focus {
        border-color: var(--pk);
        background: rgba(255, 107, 156, 0.05);
        outline: none;
        box-shadow: 0 0 0 4px rgba(255, 107, 156, 0.1);
    }

    .nb-link-btn {
        background: none;
        border: none;
        color: var(--pk);
        font-weight: 700;
        cursor: pointer;
        padding: 0;
        text-decoration: underline;
    }

    .nb-link-btn:disabled {
        color: #ccc;
        cursor: default;
        text-decoration: none;
    }

    .nb-alert {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        margin-top: 20px;
        text-align: center;
    }

    .nb-alert-danger {
        background: #fff5f5;
        color: #e53e3e;
        border: 1px solid #fed7d7;
    }

    .d-none { display: none !important; }
    .w-100 { width: 100%; }
    .mt-4 { margin-top: 1.5rem; }
    .mb-3 { margin-bottom: 1rem; }
    .text-center { text-align: center; }
    .text-sm { font-size: 0.85rem; }
    .text-muted { color: #888; }
</style>

<script>
    let loginTimer;
    window.__loginSuccessCallback = null;
    
    function openLoginModal(onSuccess = null) {
        window.__loginSuccessCallback = onSuccess;
        document.getElementById('nbLoginModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeLoginModal() {
        document.getElementById('nbLoginModal').classList.remove('show');
        document.body.style.overflow = '';
        resetLoginModal();
    }

    function resetLoginModal() {
        document.getElementById('loginStepPhone').classList.add('active');
        document.getElementById('loginStepOtp').classList.remove('active');
        document.getElementById('loginPhone').value = '';
        document.querySelectorAll('.otp-field').forEach(f => f.value = '');
        document.getElementById('loginError').classList.add('d-none');
        clearInterval(loginTimer);
    }

    function updatePageCsrf(token) {
        if (!token) return;
        document.querySelectorAll('input[name="_token"]').forEach(input => input.value = token);
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', token);
    }

    async function handleSendOtp() {
        const phone = document.getElementById('loginPhone').value;
        const btn = event.currentTarget;
        const errorEl = document.getElementById('loginError');

        if (!/^\d{10}$/.test(phone)) {
            showLoginError('Please enter a valid 10-digit phone number.');
            return;
        }

        errorEl.classList.add('d-none');
        btn.disabled = true;
        btn.querySelector('.btn-text').textContent = 'Sending...';

        try {
            const response = await fetch("{{ route('frontend.sendOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('loginStepPhone').classList.remove('active');
                document.getElementById('loginStepOtp').classList.add('active');
                document.getElementById('displayPhone').textContent = '+91 ' + phone;
                startTimer();
            } else {
                showLoginError(data.message || 'Failed to send OTP.');
            }
        } catch (err) {
            showLoginError('An error occurred. Please try again.');
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').textContent = 'Send OTP';
        }
    }

    async function handleVerifyOtp() {
        const phone = document.getElementById('loginPhone').value;
        const otp = Array.from(document.querySelectorAll('.otp-field')).map(f => f.value).join('');
        const btn = event.currentTarget;

        if (otp.length !== 6) {
            showLoginError('Please enter the 6-digit code.');
            return;
        }

        btn.disabled = true;
        btn.querySelector('.btn-text').textContent = 'Verifying...';

        try {
            const response = await fetch("{{ route('frontend.verifyOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    phone, 
                    otp,
                    redirect_to: window.location.href 
                })
            });

            const data = await response.json();

            if (data.success) {
                btn.querySelector('.btn-text').textContent = 'Success!';
                updatePageCsrf(data.csrf_token);
                
                if (window.__loginSuccessCallback) {
                    window.__loginSuccessCallback(data);
                    closeLoginModal();
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                }
            } else {
                showLoginError(data.message || 'Invalid OTP.');
                btn.disabled = false;
                btn.querySelector('.btn-text').textContent = 'Verify & Login';
            }
        } catch (err) {
            showLoginError('An error occurred. Please try again.');
            btn.disabled = false;
            btn.querySelector('.btn-text').textContent = 'Verify & Login';
        }
    }

    function showLoginError(msg) {
        const errorEl = document.getElementById('loginError');
        errorEl.textContent = msg;
        errorEl.classList.remove('d-none');
    }

    function startTimer() {
        let timeLeft = 30;
        const timerEl = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');
        
        resendBtn.disabled = true;
        clearInterval(loginTimer);
        
        loginTimer = setInterval(() => {
            timeLeft--;
            timerEl.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(loginTimer);
                resendBtn.disabled = false;
                timerEl.parentElement.innerHTML = 'Resend Now';
            }
        }, 1000);
    }

    // OTP Field handling
    document.querySelectorAll('.otp-field').forEach((field, index) => {
        field.addEventListener('keyup', (e) => {
            if (e.key >= 0 && e.key <= 9) {
                if (index < 5) field.nextElementSibling.focus();
            } else if (e.key === 'Backspace') {
                if (index > 0) field.previousElementSibling.focus();
            }
        });
    });

    // Close on click outside
    document.getElementById('nbLoginModal').addEventListener('click', function(e) {
        if (e.target === this) closeLoginModal();
    });
</script>
