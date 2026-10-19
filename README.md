# Sistema de Traslados de Pacientes (S.I.G.S.M.) - Grupo Sirius

Hospital de Clínicas. Backend en PHP + MySQL con arquitectura en 3 capas.

## Qué incluye

- Login con sesiones y roles: administrador, médico, enfermería y paciente
- Cierre de sesión (`session_destroy`) desde cualquier página con `?salir=1` y desde `logout.php`
- Solicitud de traslado (pacientes, material biológico y no biológico) con aprobación del administrador
- Documentación (CRUD) organizada por servicio y portal público con acordeones (`portal.php`)
- Código QR por documento (`ver_documento.php?codigo=...`)
- Dashboard, Ambulancias y Usuarios con datos reales de la base
- Validaciones en JavaScript en login, documentos y solicitudes
- Login con el estilo base indicado (`presentacion/css/login_sistema.css`)

## Estructura

```
config.php              nombre y datos del sistema
index.php               redirige al panel o al login
login.php / logout.php  autenticación
portal.php              portal público para pacientes (sin login)
ver_documento.php       vista pública del QR
negocio/                Usuario, Documento, Solicitud, Auth
datos/                  Conexion, UsuarioDAO, DocumentoDAO, SolicitudDAO, PanelDAO
presentacion/           páginas y layout
presentacion/css/       estilos.css (aplicación) y login_sistema.css (login)
presentacion/js/        validaciones.js
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
4. Abrir `http://localhost/modulo_documentacion/login.php`

Si la base ya estaba creada con los tres primeros scripts, solo hay que importar `sql/actualizacion_ejes.sql` (una sola vez).

## Usuarios de prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@sirius.com | admin123 |
| Médico | medico@sirius.com | medico123 |
| Enfermería | enfermeria@sirius.com | enfermeria123 |
| Paciente | maria.gonzalez@gmail.com | paciente123 |

## Qué puede hacer cada rol

- **Administrador:** todo (dashboard, solicitudes, aprobar/rechazar, ambulancias, documentos, usuarios).
- **Médico y enfermería:** crear solicitudes (ven solo las suyas) y cargar o editar documentos.
- **Paciente:** ver el listado de documentos.
- **Sin sesión:** portal de pacientes y vista del QR.

## Imagen del login

La imagen del lado izquierdo es opcional: si se agrega `presentacion/img/login.jpg` se usa
como fondo; si no existe, se ve un degradé teal.

## Pendiente

- Encuestas de satisfacción con reportes por servicio (eje 3)
- Reporte de eventos adversos (eje 4)
