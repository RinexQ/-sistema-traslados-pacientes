(function () {
    'use strict';

    var REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    var REGEX_RUTA  = /^(https?:\/\/|documentos\/)\S+$/i;

    function marcar(input, mensaje) {
        var aviso = input.parentElement.querySelector('.invalid-feedback');
        if (mensaje) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (aviso) { aviso.textContent = mensaje; }
            return false;
        }
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (aviso) { aviso.textContent = ''; }
        return true;
    }

    function validarEmail(input) {
        var valor = input.value.trim();
        if (valor === '') { return marcar(input, 'El email es obligatorio.'); }
        if (!REGEX_EMAIL.test(valor)) { return marcar(input, 'Ingresá un email válido (ejemplo: nombre@dominio.com).'); }
        return marcar(input, '');
    }

    function validarPassword(input) {
        if (input.value === '') { return marcar(input, 'La contraseña es obligatoria.'); }
        return marcar(input, '');
    }

    function validarTitulo(input) {
        var valor = input.value.trim();
        if (valor === '') { return marcar(input, 'El título es obligatorio.'); }
        if (valor.length > 200) { return marcar(input, 'El título no puede superar los 200 caracteres.'); }
        return marcar(input, '');
    }

    function validarRuta(input) {
        var valor = input.value.trim();
        if (valor === '') { return marcar(input, 'Indicá la URL o ruta del archivo.'); }
        if (valor.length > 500) { return marcar(input, 'La ruta es demasiado larga (máximo 500 caracteres).'); }
        if (!REGEX_RUTA.test(valor)) {
            return marcar(input, 'Debe comenzar con http://, https:// o documentos/ y no tener espacios.');
        }
        return marcar(input, '');
    }

    function conectar(form, campos) {
        form.addEventListener('submit', function (evento) {
            var todoOk = true;
            var primerError = null;
            campos.forEach(function (c) {
                if (!c.validar(c.input)) {
                    todoOk = false;
                    if (primerError === null) { primerError = c.input; }
                }
            });
            if (!todoOk) {
                evento.preventDefault();
                primerError.focus();
            }
        });

        campos.forEach(function (c) {
            c.input.addEventListener('blur', function () { c.validar(c.input); });
            c.input.addEventListener('input', function () {
                if (c.input.classList.contains('is-invalid')) { c.validar(c.input); }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var formLogin = document.getElementById('form-login');
        if (formLogin) {
            conectar(formLogin, [
                { input: document.getElementById('email'),    validar: validarEmail },
                { input: document.getElementById('password'), validar: validarPassword }
            ]);
        }

        var formDocumento = document.getElementById('form-documento');
        if (formDocumento) {
            conectar(formDocumento, [
                { input: document.getElementById('titulo'),      validar: validarTitulo },
                { input: document.getElementById('archivo_url'), validar: validarRuta }
            ]);
        }
    });
})();
