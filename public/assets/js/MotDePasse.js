document.addEventListener('DOMContentLoaded', function () {
    var toggleButton = document.getElementById('togglePassword');
    var passwordField = document.getElementById('password');
    var eyeIcon = document.getElementById('eyeIcon');

    if (toggleButton && passwordField && eyeIcon) {
        toggleButton.addEventListener('click', function () { // Toggle password visibility
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.src = "/assets/image/eye-solid-T_vXmAp.svg"; // Eye open
    } else {
        passwordField.type = 'password';
        eyeIcon.src = "/assets/image/eye-slash-solid-wo370My.svg"; // Eye closed
    }
    });
    }
});