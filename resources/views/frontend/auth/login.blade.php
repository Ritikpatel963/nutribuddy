<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head/>

<body>

    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="{{ asset('assets/images/auth/auth-img.png') }}" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <div>
                    <a href="{{ route('home') }}" class="mb-40 max-w-290-px">
                        <img src="{{ asset('img/logo.png') }}" alt="NutriBuddy" style="max-width: 200px;">
                    </a>
                    <h4 class="mb-12">Login or Sign Up</h4>
                    <p class="mb-32 text-secondary-light text-lg">New to NutriBuddy? Enter your phone number to create an account or login instantly with OTP.</p>
                </div>
                
                <div id="alert-container"></div>

                <form id="otpForm">
                    @csrf
                    <div id="phone-group" class="icon-field mb-16">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:phone-calling-outline"></iconify-icon>
                        </span>
                        <input type="text" id="phone" name="phone" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Phone Number" required maxlength="10">
                    </div>

                    <div id="otp-group" class="icon-field mb-16" style="display: none;">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="text" id="otp" name="otp" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Enter 6-digit OTP" maxlength="6">
                    </div>

                    <button type="button" id="sendOtpBtn" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-16">Send OTP</button>
                    <button type="button" id="verifyOtpBtn" class="btn btn-success text-sm btn-sm px-12 py-16 w-100 radius-12 mt-16" style="display: none;">Verify & Login</button>

                </form>
            </div>
        </div>
    </section>

    <x-script />

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

            // Phone and OTP input validation (allow only digits)
            phoneInput.on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            otpInput.on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            function showAlert(message, type = 'danger') {
                alertContainer.html(`
                    <div class="alert alert-${type} radius-12 mb-16 px-16 py-8 text-sm">
                        ${message}
                    </div>
                `);
            }

            function csrfToken() {
                return $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val() || '';
            }

            function updateCsrfToken(token) {
                if (!token) return;
                $('meta[name="csrf-token"]').attr('content', token);
                $('input[name="_token"]').val(token);
            }

            function refreshCsrfToken() {
                return $.ajax({
                    url: "{{ route('csrf.token') }}",
                    method: 'GET',
                    dataType: 'json'
                }).then(function(response) {
                    updateCsrfToken(response.csrf_token);
                    return response.csrf_token;
                });
            }

            function otpAjax(options) {
                const deferred = $.Deferred();
                const requestOptions = {
                    ...options,
                    data: {
                        ...(options.data || {}),
                        _token: csrfToken()
                    }
                };

                $.ajax(requestOptions)
                    .done(deferred.resolve)
                    .fail(function(xhr) {
                        if (xhr.status !== 419) {
                            deferred.reject(xhr);
                            return;
                        }

                        refreshCsrfToken()
                            .then(function() {
                                requestOptions.data._token = csrfToken();
                                return $.ajax(requestOptions);
                            })
                            .done(deferred.resolve)
                            .fail(deferred.reject);
                    });

                return deferred.promise();
            }

            sendOtpBtn.on('click', function() {
                const phone = phoneInput.val();
                if (!phone || phone.length !== 10) {
                    showAlert('Please enter a valid 10-digit phone number.');
                    return;
                }

                sendOtpBtn.prop('disabled', true).text('Sending...');

                otpAjax({
                    url: "{{ route('frontend.sendOtp') }}",
                    method: 'POST',
                    data: {
                        phone: phone
                    }
                }).done(function(response) {
                        showAlert(response.message, 'success');
                        phoneInput.prop('readonly', true);
                        sendOtpBtn.hide();
                        otpGroup.show();
                        verifyOtpBtn.show();
                }).fail(function(xhr) {
                        sendOtpBtn.prop('disabled', false).text('Send OTP');
                        const message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                        showAlert(message);
                });
            });

            verifyOtpBtn.on('click', function() {
                const phone = phoneInput.val();
                const otp = otpInput.val();

                if (!otp || otp.length !== 6) {
                    showAlert('Please enter a valid 6-digit OTP.');
                    return;
                }

                verifyOtpBtn.prop('disabled', true).text('Verifying...');

                otpAjax({
                    url: "{{ route('frontend.verifyOtp') }}",
                    method: 'POST',
                    data: {
                        phone: phone,
                        otp: otp
                    }
                }).done(function(response) {
                        showAlert(response.message, 'success');
                        updateCsrfToken(response.csrf_token);
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1000);
                }).fail(function(xhr) {
                        verifyOtpBtn.prop('disabled', false).text('Verify & Login');
                        const message = xhr.responseJSON ? xhr.responseJSON.message : 'Invalid OTP.';
                        showAlert(message);
                });
            });
        });
    </script>
</body>
</html>
