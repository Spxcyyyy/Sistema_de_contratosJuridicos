# Rendimiento del entorno Docker

El servicio PHP ejecuta Yii en modo producción. El código está en una carpeta
compartida de Windows; comprobar archivos en esa carpeta en cada solicitud puede
introducir pausas al navegar. OPcache mantiene el código compilado sin comprobar
sus fechas, y Composer genera un índice optimizado de clases.

Después de modificar archivos PHP o instalar dependencias, ejecutar:

```sh
docker compose restart php
```

Después de instalar o actualizar dependencias, Composer regenera automáticamente
el índice. Para regenerarlo manualmente:

```sh
docker compose exec -T php composer dump-autoload --optimize --no-scripts
docker compose restart php
```

La primera solicitud después del reinicio carga la caché y puede tardar más.
Los cambios de CSS no requieren reiniciar PHP.

Para desarrollo con actualización automática de PHP, cambiar en
`docker/php/opcache.ini` a `opcache.validate_timestamps=1` y
`opcache.revalidate_freq=0`, y reiniciar el servicio. Esto vuelve a comprobar
archivos en cada solicitud y puede reducir el rendimiento en Docker Desktop.
