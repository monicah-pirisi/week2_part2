$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        // Get form values
        var name = $('#name').val().trim();
        var email = $('#email').val().trim();
        var password = $('#password').val();
        var phone_number = $('#phone_number').val().trim();
        var country = $('#country').val().trim();
        var city = $('#city').val().trim();
        var role = $('input[name="role"]:checked').val() || '0'; // Default to customer

        // Validation: Check empty fields
        if (!name || !email || !password || !phone_number || !country || !city) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        // Validation: Email format
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Validation: Password strength
        if (password.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must be at least 6 characters long!',
            });
            return;
        }

        if (!password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must contain at least one lowercase letter, one uppercase letter, and one number!',
            });
            return;
        }

        // Validation: Phone number format (basic check)
        var phoneRegex = /^[\d\s\-\(\)\+]{10,15}$/;
        if (!phoneRegex.test(phone_number)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone Number',
                text: 'Please enter a valid phone number (10-15 digits)!',
            });
            return;
        }

        // Show loading state
        var submitBtn = $('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registering...');

        // AJAX request
        $.ajax({
            url: '../actions/register_user_action.php',
            type: 'POST',
            dataType: 'json',
            data: {
                name: name,
                email: email,
                password: password,
                phone_number: phone_number,
                country: country,
                city: city,
                role: role
            },
            success: function(response) {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: response.message || 'You can now log in with your credentials.',
                        confirmButtonText: 'Go to Login'
                    }).then((result) => {
                        if (result.isConfirmed || result.isDismissed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message || 'An error occurred during registration.',
                    });
                }
            },
            error: function(xhr, status, error) {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);

                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                var errorMessage = 'An unexpected error occurred. Please try again.';

                // Try to parse JSON response
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (parseError) {
                    // If response contains HTML error (like PHP errors)
                    if (xhr.responseText.includes('Parse error') || 
                        xhr.responseText.includes('Fatal error') ||
                        xhr.responseText.includes('<br />')) {
                        errorMessage = 'Server configuration error. Please contact the administrator.';
                        console.error('PHP Error detected in response:', xhr.responseText);
                    } else if (xhr.status === 404) {
                        errorMessage = 'Registration service not found. Please check the file path.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Internal server error. Please try again later.';
                    } else if (xhr.status === 0) {
                        errorMessage = 'Network error. Please check your internet connection.';
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Registration Error',
                    text: errorMessage,
                    footer: xhr.status !== 0 ? '<small>Error Code: ' + xhr.status + '</small>' : ''
                });
            }
        });
    });

    // Real-time email validation
    $('#email').on('blur', function() {
        var email = $(this).val().trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Real-time password validation
    $('#password').on('input', function() {
        var password = $(this).val();
        var strength = 0;
        var feedback = [];

        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        // Remove existing feedback
        $(this).next('.password-strength').remove();

        if (password.length > 0) {
            var strengthText = '';
            var strengthClass = '';

            if (strength < 3) {
                strengthText = 'Weak';
                strengthClass = 'text-danger';
            } else if (strength === 3) {
                strengthText = 'Fair';
                strengthClass = 'text-warning';
            } else if (strength === 4) {
                strengthText = 'Good';
                strengthClass = 'text-info';
            } else {
                strengthText = 'Strong';
                strengthClass = 'text-success';
            }

            $(this).after('<small class="password-strength ' + strengthClass + '">Password strength: ' + strengthText + '</small>');
        }
    });

    // Clear validation on input
    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});