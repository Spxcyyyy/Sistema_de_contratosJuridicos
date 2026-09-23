# Permisos por rol

Los behaviors de ContratoController, FirmaController y UserController usan
AccessPolicy::accessBehavior(). La política consulta el rol de la cuenta y
deniega cualquier acción que no esté incluida expresamente. SiteController
mantiene públicas las páginas de acceso, recuperación, contacto, información
y errores; su panel requiere una cuenta activa con un rol reconocido.
El inicio del Recabador redirige al listado de contratos; puede agregar notas y marcar firmas como firmadas dentro de un contrato.

| Acción | Administrador | Usuario | Recabador |
| --- | --- | --- | --- |
| Consultar listado y detalle de contratos | Sí | Sí | Sí |
| Consultar Firmas, historial general o del contrato y reportes | Sí | Sí | No |
| Crear y editar contratos (incluidos sus firmantes) | Sí | Sí | No |
| Eliminar contratos | Sí | Sí | No |
| Registrar firmantes desde Firmas | Sí | No | No |
| Editar o eliminar desde Firmas | Sí | No | No |
| Marcar como firmado dentro del contrato | Sí | Sí | Sí |
| Agregar notas | Sí | No | Sí |
| Administrar usuarios y restablecer contraseñas | Sí | No | No |

Las vistas usan AccessPolicy::allows() para sus botones y enlaces. Las
solicitudes directas pasan por AccessControl aunque el botón no sea visible.
VerbFilter conserva POST obligatorio para eliminar, recabar firmas, agregar
notas y cerrar sesión. Los roles desconocidos, usuarios inactivos y eliminados
no obtienen acceso a las acciones protegidas.
