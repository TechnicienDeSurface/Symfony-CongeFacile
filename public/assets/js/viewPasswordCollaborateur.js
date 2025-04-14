document.addEventListener('DOMContentLoaded', function () {
    var toggleButton = document.getElementById('togglePassword');
    var toggleButton2 = document.getElementById('togglePassword2'); 
    var toggleButton3 = document.getElementById('togglePassword3');
    var eyeIcon = document.getElementById('eyeIcon');
    var currentPassword = document.getElementById('mot_de_passe_currentPassword'); 
    var newPassword = document.getElementById('mot_de_passe_newPassword'); 
    var confirmPassword = document.getElementById('mot_de_passe_confirmPassword'); 

    if (toggleButton && eyeIcon && currentPassword) {
        toggleButton.addEventListener('click', function () { // Toggle password visibility
            if(currentPassword.type === 'password'){
                currentPassword.type = 'text' ; 
                eyeIcon.src = eyeIcon.dataset.eyeOpen;
            }else {
                currentPassword.type = 'password'; 
                eyeIcon.src = eyeIcon.dataset.eyeClosed; // Eye closed
            }
        });
    }
    if(toggleButton2 && eyeIcon && newPassword){
        toggleButton2.addEventListener('click', function (){
            if(newPassword.type === 'password'){
                newPassword.type = 'text' ; 
                eyeIcon.src = eyeIcon.dataset.eyeOpen; 
            }else{
                newPassword.type = 'password'; 
                eyeIcon.src = eyeIcoN.dataset.eyeClosed;
            } 
        });
    }
    if(toggleButton3 && eyeIcon && confirmPassword){
        toggleButton3.addEventListener('click', function (){
            if(confirmPassword.type === 'password'){
                confirmPassword.type = 'text' ; 
                eyeIcon.src = eyeIcon.dataset.eyeOpen; 
            }else{
                confirmPassword.type = 'password'; 
                eyeIcon.src = eyeIcoN.dataset.eyeClosed;
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