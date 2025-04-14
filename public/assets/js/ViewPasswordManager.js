
// Pour les informations managers
document.addEventListener('DOMContentLoaded', function () {

    function togglePasswordVisibility(buttonId, inputId, iconId) {
        const toggleButton = document.getElementById(buttonId);
        const passwordField = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);

        if (!toggleButton || !passwordField || !eyeIcon) {
            console.error(`Erreur : Élément(s) non trouvé(s) → button: ${buttonId}, input: ${inputId}, icon: ${iconId}`);
            return;
        }

        toggleButton.addEventListener('click', function () {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            eyeIcon.src = isPassword ? eyeIcon.dataset.eyeOpen : eyeIcon.dataset.eyeClosed;
        });
    }

    // Appliquer la fonction à chaque champ de mot de passe
    togglePasswordVisibility('togglePassword', 'mot_de_passe_currentPassword', 'eyeIcon');
    togglePasswordVisibility('togglePasswordNew', 'mot_de_passe_newPassword', 'eyeIconNew');
    togglePasswordVisibility('togglePasswordConfirm', 'mot_de_passe_confirmPassword', 'eyeIconConfirm');
});