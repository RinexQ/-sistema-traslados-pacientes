// Validaciones del lado del cliente (login, documentos y solicitudes de traslado).
// El servidor en PHP vuelve a validar todo, esto es solo para avisar mas rapido.
(function () {
    'use strict';

    var REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    var REGEX_RUTA = /^(https?:\/\/|documentos\/)\S+$/i;
    var REGEX_TELEFONO = /^[0-9+\s-]{7,20}$/;

    // Marca un campo con error (o bien si el mensaje viene vacio)
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

    // Saca las marcas de un campo que no hace falta validar
    function limpiar(input) {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        var aviso = input.parentElement.querySelector('.invalid-feedback');
        if (aviso) { aviso.textContent = ''; }
        return true;
    }

    // ---------- Validaciones sueltas ----------

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

    function validarRuta(input) {
        var valor = input.value.trim();
        if (valor === '') { return marcar(input, 'Indicá la URL o ruta del archivo.'); }
        if (valor.length > 500) { return marcar(input, 'La ruta es demasiado larga (máximo 500 caracteres).'); }
        if (!REGEX_RUTA.test(valor)) {
            return marcar(input, 'Debe comenzar con http://, https:// o documentos/ y no tener espacios.');
        }
        return marcar(input, '');
    }

    function validarTelefono(input) {
        if (!REGEX_TELEFONO.test(input.value.trim())) {
            return marcar(input, 'Ingresá un teléfono válido (7 a 20 caracteres, solo números, espacios, + o -).');
        }
        return marcar(input, '');
    }

    // Crea un validador de texto obligatorio con un maximo de caracteres
    function textoObligatorio(nombreCampo, maximo) {
        return function (input) {
            var valor = input.value.trim();
            if (valor === '') { return marcar(input, nombreCampo + ' es obligatorio.'); }
            if (valor.length > maximo) {
                return marcar(input, nombreCampo + ' no puede superar los ' + maximo + ' caracteres.');
            }
            return marcar(input, '');
        };
    }

    // Crea un validador para un select que no puede quedar vacio
    function selectObligatorio(mensaje) {
        return function (input) {
            if (input.value === '') { return marcar(input, mensaje); }
            return marcar(input, '');
        };
    }

    function validarVerificado(input) {
        if (!input.checked) { return marcar(input, 'Tenés que confirmar la lista de verificación.'); }
        return marcar(input, '');
    }

    // ---------- Conecta los validadores con un formulario ----------

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
            c.input.addEventListener('change', function () {
                if (c.input.classList.contains('is-invalid')) { c.validar(c.input); }
            });
        });
    }

    function porId(id) {
        return document.getElementById(id);
    }

    // ---------- Pantalla 1: login ----------

    function iniciarLogin() {
        var form = porId('form-login');
        if (!form) { return; }
        conectar(form, [
            { input: porId('email'), validar: validarEmail },
            { input: porId('password'), validar: validarPassword }
        ]);
    }

    // ---------- Pantalla 2: documentos ----------

    function iniciarDocumento() {
        var form = porId('form-documento');
        if (!form) { return; }
        conectar(form, [
            { input: porId('titulo'), validar: textoObligatorio('El título', 200) },
            { input: porId('id_servicio'), validar: selectObligatorio('Elegí el servicio del documento.') },
            { input: porId('archivo_url'), validar: validarRuta }
        ]);
    }

    // ---------- Pantalla 3: solicitud de traslado ----------

    function iniciarSolicitud() {
        var form = porId('form-solicitud');
        if (!form) { return; }

        var tipo = porId('tipo');
        var bloquePaciente = porId('bloque-paciente');
        var aislamiento = porId('aislamiento');
        var detalle = porId('aislamiento_detalle');

        function esPaciente() {
            return tipo.value === 'paciente';
        }

        // Los datos del paciente solo se piden si se traslada un paciente
        function soloSiPaciente(validador) {
            return function (input) {
                if (!esPaciente()) { return limpiar(input); }
                return validador(input);
            };
        }

        // El detalle del aislamiento solo se pide si marco "requiere aislamiento"
        function validarDetalle(input) {
            if (!esPaciente() || !aislamiento.checked) { return limpiar(input); }
            return textoObligatorio('El detalle del aislamiento', 150)(input);
        }

        function actualizarPantalla() {
            bloquePaciente.style.display = esPaciente() ? 'block' : 'none';
            detalle.style.display = aislamiento.checked ? 'block' : 'none';
        }

        tipo.addEventListener('change', actualizarPantalla);
        aislamiento.addEventListener('change', function () {
            actualizarPantalla();
            validarDetalle(detalle);
        });
        actualizarPantalla();

        conectar(form, [
            { input: porId('origen'), validar: textoObligatorio('El origen', 200) },
            { input: porId('destino'), validar: textoObligatorio('El destino', 200) },
            { input: porId('motivo'), validar: textoObligatorio('El motivo', 255) },
            { input: porId('ci_paciente'), validar: soloSiPaciente(selectObligatorio('Elegí el paciente a trasladar.')) },
            { input: porId('domicilio'), validar: soloSiPaciente(textoObligatorio('El domicilio', 200)) },
            { input: porId('telefono'), validar: soloSiPaciente(validarTelefono) },
            { input: porId('diagnostico'), validar: soloSiPaciente(textoObligatorio('El diagnóstico', 255)) },
            { input: detalle, validar: validarDetalle },
            { input: porId('verificado'), validar: soloSiPaciente(validarVerificado) }
        ]);
    }

    document.addEventListener('DOMContentLoaded', function () {
        iniciarLogin();
        iniciarDocumento();
        iniciarSolicitud();
    });
})();
