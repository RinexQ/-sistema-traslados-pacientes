# Sistema de Traslados de Pacientes — Módulo Documentación

Segunda entrega. Backend en PHP + MySQL.

## Estado de esta entrega

- [x] Base de datos corregida y creada con DDL completo
- [x] Login con sesiones ($_SESSION), dos roles: administrador y paciente
- [x] Logout que destruye la sesión
- [x] Rutas protegidas: redirigen a /login.php si no hay sesión activa
- [x] CRUD completo de Documentación (alta, listado, edición, baja física)
- [x] Vista pública accesible por QR (ver_documento.php)
- [x] Arquitectura en 3 capas (presentacion / negocio / datos)
- [x] 2 clases de dominio con atributos y métodos: `Usuario`, `Documento`
- [ ] Módulo Ambulancias (opcional, no incluido)

## Estructura de carpetas

```
/
├── login.php              controlador de login
├── logout.php             destruye la sesión
├── ver_documento.php       vista pública que abre el QR (sin sesión)
├── negocio/                capa de NEGOCIO (lógica, validaciones, dominio)
│   ├── Usuario.php
│   ├── Documento.php
│   └── Auth.php
├── datos/                  capa de DATOS (acceso a la base)
│   ├── Conexion.php
│   ├── UsuarioDAO.php
│   └── DocumentoDAO.php
└── presentacion/            capa de PRESENTACIÓN
    ├── index.html           prototipo original
    ├── styles.css           prototipo original
    ├── panel.php            sirve index.html solo si hay sesión activa
    ├── documentos_listar.php
    ├── documento_form.php   alta y edición
    └── documento_eliminar.php
```

## Instalación (XAMPP)

1. Copiar toda esta carpeta dentro de `htdocs` de XAMPP, por ejemplo
   `htdocs/traslados` (con Apache y MySQL corriendo).
2. En phpMyAdmin, importar en este orden (en caso de tener los archivos correspondientes para la importación):
   - `traslados_pacientes.sql` (crea la base y las tablas)
   - `agregar_login.sql` (agrega las contraseñas y el usuario de prueba)
3. Revisar `datos/Conexion.php` si tu MySQL no usa usuario `root` sin
   contraseña (configuración por defecto de XAMPP).
4. Abrir en el navegador: `http://localhost/traslados/login.php`

## Usuario de prueba

- Email: `admin@medicare.com`
- Contraseña: `admin123`

## Notas de diseño

- `index.html` y `styles.css` no se tocaron. La ruta protegida real del
  dashboard es `presentacion/panel.php`, que valida la sesión y después
  sirve el HTML original tal cual.
- El campo `password_hash` se agregó a `administrativo` y `paciente`
  porque el modelo original no tenía forma de autenticar usuarios.
- El alta de documentos solo la puede hacer un administrador (se valida
  el rol de la sesión antes del INSERT).
