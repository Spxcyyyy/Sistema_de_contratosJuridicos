# Eliminación lógica

Aplicar la migración m260923_120000_agregar_estado_eliminacion_logica antes de usar esta versión:

    docker compose exec php php yii migrate

Las tablas contratos, firmas, user, contrato_nota, password_reset_request y
contrato_actividad incluyen status_registro: activo o eliminado. Los estados
de negocio (firmado, pendiente, cuenta activa/inactiva) se conservan.

SoftDeleteRecord::delete() actualiza el registro dentro de una transacción,
mantiene los eventos de auditoría y no ejecuta DELETE. deleteAll() utiliza
el mismo flujo. Al eliminar un contrato se marcan sus firmas y notas; al
eliminar un usuario se marcan sus solicitudes de restablecimiento.

SoftDeleteQuery excluye los eliminados al preparar el SQL, incluso después
de where() o findOne(). Firmas, notas e historial requieren un contrato
visible; las solicitudes requieren un usuario visible. Así, listados, URLs,
contadores, relaciones y reportes usan el mismo criterio.

Para mantenimiento interno se puede consultar Modelo::find()->withDeleted().
Esta opción no se expone en formularios. Los códigos de contrato, usuarios y
correos conservados siguen reservados por sus restricciones de unicidad.

La migración inversa se rechaza si existen registros eliminados para evitar
que vuelvan a aparecer inadvertidamente.
