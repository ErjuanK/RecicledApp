function validarRegistro() {
    var nombre = document.getElementById('nombre').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var tipoUsuario = document.getElementById('tipo_usuario').value;

    if (nombre.trim() === '' || email.trim() === '' || password.trim() === '' || confirmPassword.trim() === '' || tipoUsuario.trim() === '') {
        alert('Por favor, complete todos los campos.');
        return false;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Por favor, introduzca un correo electrónico válido.');
        return false;
    }

    if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden.');
        return false;
    }

    return true;
}

function validarLogin() {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    if (email.trim() === '' || password.trim() === '') {
        alert('Por favor, complete todos los campos.');
        return false;
    }

    return true;
}
