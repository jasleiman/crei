# Entorno de desarrollo con Docker

Stack: **PHP 8.3 + Apache** y **MySQL 8**, con el backup `bd/crei_integracion.sql` importado al iniciar por primera vez.

> PhpSpreadsheet 1.30+ requiere **PHP 8.3** (por `zipstream-php` 3.x). Si cambiaste el Dockerfile, reconstruí: `docker compose up -d --build`.

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Mac/Windows) o Docker Engine + Compose

## Inicio rápido

```bash
cd /ruta/al/proyecto/crei

# Opcional: variables personalizadas
cp .env.example .env

# Permisos de escritura (logs y caché de CodeIgniter)
chmod -R 777 application/logs application/cache

# Construir e iniciar (la primera vez importa el SQL; puede tardar varios minutos)
docker compose up -d --build
```

Abrir en el navegador: **http://localhost:8080/**

## Servicios

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| `web`    | 8080   | Aplicación CodeIgniter |
| `mysql`  | 3306   | Base `crei_integracion` |

### Credenciales por defecto (desarrollo)

| Variable | Valor |
|----------|--------|
| Base de datos | `crei_integracion` |
| Usuario app / MySQL | `crei` |
| Contraseña | `crei_dev` |
| Root MySQL | `root_dev` |

Conectar con cliente SQL (DBeaver, TablePlus, etc.):

- Host: `127.0.0.1`
- Puerto: `3306`
- Usuario: `crei` / contraseña `crei_dev`

## Comandos útiles

```bash
# Ver logs
docker compose logs -f web
docker compose logs -f mysql

# Parar
docker compose down

# Parar y borrar volumen MySQL (vuelve a importar el .sql al subir de nuevo)
docker compose down -v
docker compose up -d

# Entrar al contenedor web
docker compose exec web bash

# Consola MySQL
docker compose exec mysql mysql -u crei -pcrei_dev crei_integracion
```

## Variables de entorno

Definidas en `.env` (ver `.env.example`):

- `WEB_PORT`, `MYSQL_PORT`
- `APP_BASE_URL` — debe terminar en `/` (ej. `http://localhost:8080/`)
- `MYSQL_*` — credenciales del contenedor MySQL

La app lee `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` y `APP_BASE_URL` desde el contenedor `web`. En producción no se definen y se usan los valores de `application/config/`.

## Importar de nuevo el backup

El SQL solo se ejecuta cuando el volumen `mysql_data` está vacío:

```bash
docker compose down -v
docker compose up -d
```

## Problemas frecuentes

**Error de permisos en `application/logs`**

```bash
chmod -R 777 application/logs application/cache
```

**MySQL no termina de iniciar**

Revisar `docker compose logs mysql`. El dump es grande; esperar hasta que el healthcheck pase.

**Página en blanco o 500**

```bash
docker compose logs web
```

Comprobar que `APP_BASE_URL` coincida con la URL que usás en el navegador.

**PhpSpreadsheet / vendor**

Las dependencias PHP van en `application/`, no en la raíz del proyecto (requiere **PHP 8.3+** en la máquina donde ejecutás Composer, o usar el contenedor):

```bash
cd application
composer install
```

Si Composer avisa de versión de PHP, comprobá: `php -v` (debe ser ≥ 8.3) o instalá desde Docker tras `docker compose up -d --build`.

Dentro de Docker (si instalás Composer en el contenedor o lo montás):

```bash
docker compose exec web bash -c "cd application && composer install"
```
