# Backup diario de base de datos

## Qué hace

- Ejecuta `mysqldump` de la base `crei_integracion` (o la definida en `.env`).
- Comprime con **gzip** (`nombre_YYYYMMDD_HHMMSS.sql.gz`).
- Guarda en **`/var/backups/crei`** (fuera del proyecto y del webroot).
- Permisos: carpeta `700`, archivos `600`.
- Borra backups más viejos que `BACKUP_RETENTION_DAYS` (por defecto 14).

## Uso manual

```bash
chmod +x scripts/backup-database.sh
sudo mkdir -p /var/backups/crei
sudo chown "$(whoami)" /var/backups/crei   # o el usuario del cron
./scripts/backup-database.sh
```

## Cron (diario a las 03:00)

```bash
crontab -e
```

Agregar (ajustar la ruta del proyecto). **No** redirigir a `/dev/null`; el script escribe en `scripts/backup.log`:

```cron
0 3 * * * /Users/juan/desarrollo/crei/scripts/backup-database.sh
```

Ver el log:

```bash
tail -f scripts/backup.log
```

En producción:

```cron
0 3 * * * /var/www/crei/scripts/backup-database.sh
```

## Variables en `.env`

Podés usar **`scripts/.env`** (junto al script) o **`.env`** en la raíz del proyecto.  
Si existen ambos, gana el de `scripts/`.

| Variable | Descripción |
|----------|-------------|
| `BACKUP_DIR` | Carpeta destino (default `/var/backups/crei`) |
| `BACKUP_LOG` | Archivo de log (default `scripts/backup.log`) |
| `BACKUP_RETENTION_DAYS` | Días a conservar (default `14`) |
| `MYSQL_CONTAINER` | Contenedor Docker (default `crei-mysql`) |
| `MYSQL_*` | Credenciales (las mismas que `docker-compose`) |

Si el contenedor Docker está corriendo, usa `docker exec`. Si no, intenta `mysqldump` contra `127.0.0.1`.

## Restaurar un backup

```bash
gunzip -c /var/backups/crei/crei_integracion_20260529_030001.sql.gz | docker exec -i crei-mysql mysql -uroot -p"TU_ROOT_PASSWORD" crei_integracion
```
