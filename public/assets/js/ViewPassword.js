document.addEventListener('DOMContentLoaded', function () {
    var toggleButton = document.getElementById('togglePassword');
    var passwordField = document.getElementById('password');
    var eyeIcon = document.getElementById('eyeIcon');

    if (toggleButton && passwordField && eyeIcon) {
        toggleButton.addEventListener('click', function () { // Toggle password visibility
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.src = eyeIcon.dataset.eyeOpen;
    } else {
        passwordField.type = 'password';
        eyeIcon.src = eyeIcon.dataset.eyeClosed; // Eye closed
    }
    });
    }

    var toggleConfirmButton = document.getElementById('toggleComfirmPassword');
    var confirmPasswordField = document.getElementById('comfirmPassword');
    var confirmEyeIcon = document.querySelector('#toggleComfirmPassword img');

    if (toggleConfirmButton && confirmPasswordField && confirmEyeIcon) {
        toggleConfirmButton.addEventListener('click', function () {
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                confirmEyeIcon.src = confirmEyeIcon.dataset.eyeOpen;
            } else {
                confirmPasswordField.type = 'password';
                confirmEyeIcon.src = confirmEyeIcon.dataset.eyeClosed;
            }
        });
    }
});