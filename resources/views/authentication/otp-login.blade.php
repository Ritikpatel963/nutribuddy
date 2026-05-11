<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head/>

<body>
    <style>
        .otp-login-wrap {
            max-width: 464px;
        }

        @media (max-width: 575.98px) {
            .auth-right {
                min-height: 100vh;
                padding: 24px 16px !important;
            }

            .otp-login-wrap {
                max-width: 100%;
            }

            .icon-field .form-control {
                font-size: 16px;
                padding-left: 44px;
            }

            #sendOtpBtn,
            #verifyOtpBtn {
                min-height: 52px;
                white-space: normal;
            }
        }
    </style>

    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="{{ asset('assets/images/auth/auth-img.png') }}" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100 otp-login-wrap">
                <div>
                    <a href="{{ route('home') }}" class="mb-40 max-w-290-px">
                        <img src="{{ asset('img/logo.png') }}" alt="NutriBuddy" style="max-width: 200px;">
                    </a>
                    <h4 class="mb-12">Login with OTP</h4>
                    <p class="mb-32 text-secondary-light text-lg">Enter your phone number to receive a one-time password.</p>
                </div>
                
                <div id="alert-container"></div>

                <form id="otpForm">
                    @csrf
                    <div id="phone-group" class="icon-field mb-16">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:phone-calling-outline"></iconify-icon>
                        </span>
                        <input type="text" id="phone" name="phone" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Phone Number (10 digits)" required maxlength="10" inputmode="numeric" pattern="[0-9]*" autocomplete="tel" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" onkeydown="return event.ctrlKey || event.metaKey || ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'].includes(event.key) || /^[0-9]$/.test(event.key)" onpaste="event.preventDefault(); this.value=(this.value + (event.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g,'')).slice(0,10)" ondrop="event.preventDefault()">
                    </div>

                    <div id="otp-group" class="icon-field mb-16" style="display: none;">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="text" id="otp" name="otp" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Enter 6-digit OTP" maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)" onkeydown="return event.ctrlKey || event.metaKey || ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'].includes(event.key) || /^[0-9]$/.test(event.key)" onpaste="event.preventDefault(); this.value=(this.value + (event.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g,'')).slice(0,6)" ondrop="event.preventDefault()">
                    </div>

                    <button type="button" id="sendOtpBtn" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-16">Send OTP</button>
                    <button type="button" id="verifyOtpBtn" class="btn btn-success text-sm btn-sm px-12 py-16 w-100 radius-12 mt-16" style="display: none;">Verify & Login</button>

                    <div class="mt-32 text-center text-sm">
                        <p class="mb-0">Already have an account? <a href="{{ route('signin') }}" class="text-primary-600 fw-semibold">Sign In with Password</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <x-script />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function forceNumeric(id, maxLength) {
                const input = document.getElementById(id);
                if (!input) return;

                const clean = function() {
                    input.value = input.value.replace(/[^0-9]/g, '').slice(0, maxLength);
                };

                input.addEventListener('beforeinput', function(event) {
                    if (event.data && /[^0-9]/.test(event.data)) {
                        event.preventDefault();
                    }
                });

                input.addEventListener('input', clean);
                input.addEventListener('keyup', clean);
                input.addEventListener('change', clean);
            }

            forceNumeric('phone', 10);
            forceNumeric('otp', 6);
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const sendOtpBtn = $('#sendOtpBtn');
            const verifyOtpBtn = $('#verifyOtpBtn');
            const phoneGroup = $('#phone-group');
            const otpGroup = $('#otp-group');
            const phoneInput = $('#phone');
            const otpInput = $('#otp');
            const alertContainer = $('#alert-container');

            function showAlert(message, type = 'danger') {
                alertContainer.html(`
                    <div class="alert alert-${type} radius-12 mb-16 px-16 py-8 text-sm">
                        ${message}
                    </div>
                `);
            }

            function digitsOnly(input, maxLength) {
                input.val(input.val().replace(/\D/g, '').slice(0, maxLength));
            }

            function blockNonNumericInput(event) {
                const data = event.originalEvent && event.originalEvent.data;

                if (data && /\D/.test(data)) {
                    event.preventDefault();
                }
            }

            function allowOnlyNumberKeys(event) {
                const allowedKeys = [
                    'Backspace',
                    'Delete',
                    'Tab',
                    'ArrowLeft',
                    'ArrowRight',
                    'Home',
                    'End'
                ];

                if (allowedKeys.includes(event.key) || event.ctrlKey || event.metaKey) {
                    return;
                }

                if (!/^\d$/.test(event.key)) {
                    event.preventDefault();
                }
            }

            function pasteDigitsOnly(event, input, maxLength) {
                event.preventDefault();

                const pastedText = (event.originalEvent.clipboardData || window.clipboardData).getData('text');
                const currentValue = input.val();
                const selectionStart = input[0].selectionStart || 0;
                const selectionEnd = input[0].selectionEnd || 0;
                const nextValue = currentValue.slice(0, selectionStart)
                    + pastedText.replace(/\D/g, '')
                    + currentValue.slice(selectionEnd);

                input.val(nextValue.slice(0, maxLength));
            }

            phoneInput.on('input', function() {
                digitsOnly(phoneInput, 10);
            });

            phoneInput.on('beforeinput', blockNonNumericInput);
            phoneInput.on('keydown', allowOnlyNumberKeys);
            phoneInput.on('paste', function(event) {
                pasteDigitsOnly(event, phoneInput, 10);
            });
            phoneInput.on('drop', function(event) {
                event.preventDefault();
            });

            otpInput.on('input', function() {
                digitsOnly(otpInput, 6);
            });

            otpInput.on('beforeinput', blockNonNumericInput);
            otpInput.on('keydown', allowOnlyNumberKeys);
            otpInput.on('paste', function(event) {
                pasteDigitsOnly(event, otpInput, 6);
            });
            otpInput.on('drop', function(event) {
                event.preventDefault();
            });

            sendOtpBtn.on('click', function() {
                digitsOnly(phoneInput, 10);
                const phone = phoneInput.val();
                if (!/^\d{10}$/.test(phone)) {
                    showAlert('Please enter a valid 10-digit phone number.');
                    return;
                }

                sendOtpBtn.prop('disabled', true).text('Sending...');

                $.ajax({
                    url: "{{ route('sendOtp') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        phone: phone
                    },
                    success: function(response) {
                        showAlert(response.message, 'success');
                        phoneInput.prop('readonly', true);
                        sendOtpBtn.hide();
                        otpGroup.show();
                        verifyOtpBtn.show();
                        
                    },
                    error: function(xhr) {
                        sendOtpBtn.prop('disabled', false).text('Send OTP');
                        const message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                        showAlert(message);
                    }
                });
            });

            verifyOtpBtn.on('click', function() {
                digitsOnly(phoneInput, 10);
                digitsOnly(otpInput, 6);
                const phone = phoneInput.val();
                const otp = otpInput.val();

                if (!/^\d{6}$/.test(otp)) {
                    showAlert('Please enter a valid 6-digit OTP.');
                    return;
                }

                verifyOtpBtn.prop('disabled', true).text('Verifying...');

                $.ajax({
                    url: "{{ route('verifyOtp') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        phone: phone,
                        otp: otp
                    },
                    success: function(response) {
                        showAlert(response.message, 'success');
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1000);
                    },
                    error: function(xhr) {
                        verifyOtpBtn.prop('disabled', false).text('Verify & Login');
                        const message = xhr.responseJSON ? xhr.responseJSON.message : 'Invalid OTP.';
                        showAlert(message);
                    }
                });
            });
        });
    </script>
</body>
</html>
