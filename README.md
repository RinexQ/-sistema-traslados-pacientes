# Sistema de Traslados de Pacientes (S.I.G.S.M.) - Grupo Sirius

Segunda entrega. Backend en PHP + MySQL con arquitectura en 3 capas.

## Estado de la entrega

- Base de datos corregida (DER y esquema), DDL completo y datos de prueba
- Conexion con PDO y consultas SELECT mostradas en el dashboard
- Login con sesiones y dos roles (administrador y paciente), logout, rutas protegidas
- Validaciones en JavaScript en login y en el formulario de documentos
- CRUD de Documentacion (alta, listado con busqueda, edicion, baja)
- Vista publica por QR (`ver_documento.php?codigo=...`)
- Dashboard, Ambulancias (vehiculos y traslados) y Usuarios con datos reales
- Modulo Ambulancias: solo lectura (opcional en la consigna)

## Estructura

```
config.php              nombre del sistema
index.php               redirige al dashboard o al login
login.php / logout.php  autenticacion
ver_documento.php       vista publica del QR
negocio/                Usuario, Documento, Auth
datos/                  Conexion, UsuarioDAO, DocumentoDAO, PanelDAO
presentacion/           paginas, layout, styles.css, js/validaciones.js
sql/                    scripts DDL y DML
```

## Instalacion (XAMPP)

1. Copiar la carpeta dentro de `C:\xampp\htdocs\` (ej: `htdocs\modulo_documentacion`).
2. Iniciar Apache y MySQL desde el panel de XAMPP.
3. En phpMyAdmin importar, en este orden:
   1. `sql/traslados_pacientes_v2.sql`
   2. `sql/agregar_login.sql`
   3. `sql/datos_prueba_ampliado.sql` (o `sql/datos_prueba.sql`, usar solo uno de los dos)
4. Si MySQL tiene otra clave, ajustar `datos/Conexion.php`.
5. Abrir `http://localhost/modulo_documentacion/login.php`

## Usuarios de prueba

- Administrador: `admin@medicare.com` / `admin123`
- Paciente: `maria.gonzalez@gmail.com` / `paciente123`

El paciente solo ve el listado de documentos (sin editar ni borrar).
