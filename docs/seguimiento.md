# Panel, vencimientos e historial

## Reportes de contratos seleccionados

En **Contratos**, marca las casillas y pulsa **Reporte de seleccionados**.
La casilla del encabezado marca únicamente la página visible. Puedes acumular
hasta 500 contratos entre páginas y filtros en la misma pestaña; **Limpiar
selección** vacía el conjunto. Antes de descargar se muestran los códigos
seleccionados y puedes elegir columnas y formato PDF, Excel o CSV.
Este reporte usa exactamente la selección, sin aplicar fechas adicionales.
El botón **Generar reporte** conserva la opción general por rango de fechas.

Las selecciones vacías, inválidas o con contratos eliminados se rechazan sin
exportar otros contratos. Excel y CSV utilizan PhpSpreadsheet (instalado con
Composer).

## Seguimiento

Al iniciar sesión, **Inicio** muestra contratos totales, contratos en proceso,
firmas pendientes, contratos concluidos, vencidos y próximos a vencer. Las
tarjetas abren el listado con el filtro correspondiente. La tarjeta de firmas
cuenta firmas individuales; su enlace muestra los contratos que las contienen.

La **fecha límite** es opcional y se captura al crear o editar un contrato.
Los contratos existentes mantienen la fecha vacía hasta que se les asigne una.
Los avisos internos aparecen desde siete días antes, incluyendo el día límite.
Al día siguiente se consideran vencidos. Se utiliza la zona horaria del sistema
(America/Mexico_City). Los estados Firmado, Todas las firmas recabadas y Cancelado
no generan avisos. Los avisos se recalculan al abrir el panel o el contrato;
no se envían correos ni notificaciones del navegador.

El **historial** registra altas, modificaciones y eliminaciones del contrato,
sus firmantes y sus notas realizadas mediante los modelos de la aplicación.
Muestra usuario, fecha y valores anteriores/nuevos. El detalle del contrato
muestra 20 eventos por página; el historial general se abre desde Inicio.
Se conserva el código del contrato y el nombre del usuario aun si se eliminan.
El registro comienza al instalar esta actualización: no reconstruye actividad
anterior ni captura cambios directos en SQL, `updateAll` o `deleteAll`.

Editar un contrato conserva los identificadores, estados y fechas de sus
firmantes. Quitar un firmante de la edición lo elimina y deja registro.
Agregar una firma pendiente reabre contratos completados automáticamente;
los contratos marcados manualmente Firmado o Cancelado conservan ese estado.

## Instalación en otro entorno

```sh
docker compose exec -T php php yii migrate --interactive=0
docker compose restart php
```

La migración agrega `contratos.fecha_vencimiento` y `contrato_actividad`, con
índices para vencimientos e historial. Revertirla elimina la fecha y el historial.
