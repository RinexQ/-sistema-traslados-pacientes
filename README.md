# Sistema de Traslados de Pacientes (S.I.G.S.M.) - Grupo Sirius

Hospital de Clínicas. Backend en PHP + MySQL con arquitectura en 3 capas.

## Qué incluye

- Login con **cédula**, contraseña y captcha; roles: administrador, médico, enfermería y paciente
- Cierre de sesión (`session_destroy`) desde cualquier página con `?salir=1` y desde `logout.php`
- **Documentación** organizada por especialidad (servicio): alta, edición, baja, descarga y portal público (`portal.php`)
  - Formatos permitidos: PDF o imagen JPG
  - Cada documento tiene un QR (se ve y se descarga como PNG); al editar el documento se genera un QR nuevo
  - Pueden gestionar documentos el administrador, el médico y enfermería
- **Encuestas de satisfacción** (`encuesta.php`): sin login, con verificación de cédula contra pacientes, captcha y
  un plazo de 30 días para volver a responder la misma encuesta; reporte para el personal
- **Solicitudes de traslado** con tipos (paciente, órgano, muestra biológica, cadetería, materiales) y prioridades
  (órganos, diálisis, tratamiento, alta a domicilio, horario específico)
- **Ambulancias:** el administrador asigna el traslado de una solicitud aprobada y marca vehículos como rotos;
  no se puede asignar un vehículo roto, ni sin chofer, ni sin enfermero si el vehículo lo requiere
- Validaciones en JavaScript y en PHP

## Estructura

```
config.php              nombre del sistema y plazo entre respuestas de encuesta
index.php               redirige al panel o al login
login.php / logout.php  autenticación
portal.php              portal público para pacientes
encuesta.php            encuesta pública
descargar.php           descarga de documentos
ver_documento.php       vista pública del QR
documentos/             archivos guardados en el servidor (ejemplo incluido)
negocio/                clases de dominio y reglas
datos/                  acceso a la base (PDO)
presentacion/           páginas internas, css y js
sql/                    scripts DDL y DML
```

## Instalación (XAMPP)

1. Copiar la carpeta dentro de `C:\xampp\htdocs\`.
2. Iniciar Apache y MySQL.
3. En phpMyAdmin importar en este orden:
   1. `sql/traslados_pacientes_v2.sql`
   2. `sql/agregar_login.sql`
   3. `sql/datos_prueba_ampliado.sql` (o `sql/datos_prueba.sql`, solo uno)
   4. `sql/actualizacion_ejes.sql`
   5. `sql/actualizacion_requerimientos.sql`
4. Abrir `http://localhost/modulo_documentacion/login.php`

Si la base ya tenía los scripts 1 a 4, importar solo `sql/actualizacion_requerimientos.sql` (una única vez).

## Usuarios de prueba

| Rol | Cédula | Contraseña |
|-----|--------|------------|
| Administrador | 10000001 | admin123 |
| Médico | 10000004 | medico123 |
| Enfermería | 10000005 | enfermeria123 |
| Paciente | 11111111 | paciente123 |

Las cédulas del personal se asignaron automáticamente (10000000 + id) y se ven en la pantalla Usuarios.

## Qué puede hacer cada rol

- **Administrador:** todo (dashboard, solicitudes, aprobar/rechazar, asignar traslados, ambulancias, documentos, encuestas, usuarios).
- **Médico y enfermería:** crear solicitudes (ven solo las suyas), gestionar documentos y ver el reporte de encuestas.
- **Paciente:** ver y descargar documentos.
- **Sin sesión:** portal de pacientes, encuestas y vista del QR.

## Notas

- Imagen opcional del login: `presentacion/img/login.jpg`.
- El QR apunta a la dirección desde donde se abrió el sistema (en `localhost` solo funciona en esa computadora).
- Pendiente: reporte de eventos adversos, seguimiento en tiempo real y aviso por desvío de ruta.
