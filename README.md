# Sistema para manejo de contratos jurídicos

Sistema para registrar contratos jurídicos, administrar firmantes y dar seguimiento a firmas, notas, historial y reportes.

## Funcionalidades

- Alta, edición y consulta de contratos: código, encargado, nomenclatura, fechas y costo.
- Registro de firmantes dentro del contrato y marcado de firmas como realizadas.
- Filtros por los estados existentes y por fecha en el listado de contratos.
- Avisos de vencimiento y panel de seguimiento según el rol.
- Notas e historial de actividad asociado a cada contrato.
- Reportes en PDF, Excel y CSV.
- Administración de usuarios y solicitudes de restablecimiento de contraseña.
- URLs sencillas: contratos identificados por su código; usuarios y firmas mediante referencias públicas.
- Eliminación lógica: los registros permanecen en la base con `status_registro = 'eliminado'` y se ocultan en las consultas normales.

## Tecnologías

- PHP 8.2 o superior según `composer.json`; Docker incluye PHP 8.5 con Apache.
- Yii 2, Bootstrap 5 y JavaScript.
- MariaDB 11 en Docker y Composer para dependencias PHP.
- Dompdf para PDF y PhpSpreadsheet para Excel y CSV.

Las versiones concretas de las dependencias están fijadas en `composer.lock`.

## Permisos

| Acción | Administrador | Usuario | Recabador |
| --- | --- | --- | --- |
| Ver listado y detalle de contratos | Sí | Sí | Sí |
| Crear y editar contratos y sus firmantes | Sí | Sí | No |
| Eliminar contratos de forma lógica | Sí | Sí | No |
| Marcar como firmado dentro del contrato | Sí | Sí | Sí |
| Agregar notas al contrato | Sí | No | Sí |
| Consultar historial y reportes | Sí | Sí | No |
| Consultar el módulo independiente de Firmas | Sí | Sí | No |
| Crear, editar o eliminar desde el módulo Firmas | Sí | No | No |
| Administrar usuarios y restablecer contraseñas | Sí | No | No |

El Recabador entra directamente al listado de contratos. Los permisos se aplican en las vistas y en los controladores mediante `components/AccessPolicy.php`. Las acciones de eliminación y marcado de firmas requieren POST.

## Instalación local con Docker

Necesitas Git, Docker Desktop o Docker Engine con Compose y acceso a Internet para descargar imágenes y dependencias. Para clonar el repositorio privado necesitas acceso a él.

### 1. Clonar el proyecto

```sh
git clone https://github.com/Spxcyyyy/Sistema_de_contratosJuridicos.git
cd Sistema_de_contratosJuridicos
```

### 2. Iniciar los servicios e instalar dependencias

```sh
docker compose up -d
docker compose exec -T php composer install --prefer-dist --no-interaction
```

### 3. Aplicar las migraciones

Cuando MariaDB esté lista, ejecuta:

```sh
docker compose exec -T php php yii migrate --interactive=0
```

### 4. Crear el primer administrador

Sustituye el nombre, correo y contraseña del ejemplo por tus propios valores:

```sh
docker compose exec -T php php yii user/create-admin administrador admin@example.com "CAMBIA_ESTA_CLAVE"
```

La contraseña debe tener entre 8 y 50 caracteres. El comando valida el usuario antes de guardarlo.

### 5. Abrir la aplicación

```sh
docker compose restart php
```

Abre **http://localhost:8000**.

Si el puerto 8000 o 3306 está ocupado, cambia el puerto del host en `docker-compose.yml`. PHP se conecta a MariaDB mediante el servicio `db`, independientemente del puerto publicado en el host.

## Configuración y datos

- `docker-compose.yml` configura la base local `yii2db` y el usuario de desarrollo.
- `config/db.php` contiene la conexión de la aplicación y la consola; debe coincidir con MariaDB.
- Las migraciones crean y actualizan las tablas.
- El volumen `db_data` conserva los datos entre reinicios. `docker compose down` detiene el entorno; añadir `-v` elimina sus volúmenes y los datos almacenados.
- El correo usa `useFileTransport = true`: se guarda en `runtime/mail`.
- La zona horaria configurada es `America/Mexico_City`.

Las credenciales incluidas son de desarrollo. Para desplegar en otro entorno, configura credenciales propias, una clave `cookieValidationKey` exclusiva, HTTPS y el transporte de correo que corresponda. El directorio público del servidor debe ser `web/`.

### Cambios durante el desarrollo

El entorno incluido tiene OPcache con comprobación de fechas desactivada. Después de cambiar PHP o instalar dependencias, ejecuta:

```sh
docker compose restart php
```

Para habilitar actualización automática, consulta [la configuración de Docker y OPcache](docker/README.md).

## Validaciones y eliminación lógica

Las reglas del modelo se ejecutan al guardar. Los campos de texto de los formularios tienen un máximo de 50 caracteres. Los nombres de encargado y firmantes aceptan letras, acentos, ñ y espacios; rechazan números y símbolos. Las restricciones del navegador se complementan con validación en el servidor.

Si falla un formulario de contrato, conserva los firmantes capturados y muestra los errores de nombre junto a su campo. El contrato y sus firmas se guardan dentro de una transacción.

Al eliminar un contrato también se marcan como eliminadas sus firmas y notas. El estado de eliminación se guarda separado del estado de negocio. Puedes consultar los contratos eliminados directamente en la base:

```sql
SELECT id, codigo, estado, status_registro
FROM contratos
WHERE status_registro = 'eliminado';
```

Consulta [la documentación de eliminación lógica](docs/eliminacion-logica.md).

## Estructura

| Carpeta | Contenido |
| --- | --- |
| `commands/` | Comandos de consola y creación del administrador |
| `components/` | Política de permisos y URLs públicas |
| `config/` | Configuración web, consola, rutas y base de datos |
| `controllers/` | Acciones de contratos, firmas, usuarios y acceso |
| `models/` | Reglas, consultas, eliminación lógica y auditoría |
| `migrations/` | Evolución del esquema de la base de datos |
| `views/` | Vistas y formularios |
| `web/` | Entrada pública, CSS, JavaScript e imágenes |
| `docs/` | Documentación del funcionamiento |
| `docker/` | Configuración del entorno Docker |
| `runtime/` | Archivos generados, registros y correo local |

## Documentación adicional

- [Permisos por rol](docs/permisos-roles.md)
- [Seguimiento de contratos](docs/seguimiento.md)
- [Eliminación lógica](docs/eliminacion-logica.md)
- [Rendimiento del entorno Docker](docker/README.md)

## Licencia

El proyecto parte de la plantilla Yii 2 Basic y conserva su [licencia BSD de tres cláusulas](LICENSE.md).
