#!/usr/bin/env bash
#
# Backup diario de MySQL (CREI integración).
# Uso: ./scripts/backup-database.sh
# Cron (ej. 03:00): 0 3 * * * /ruta/al/proyecto/crei/scripts/backup-database.sh
# El log queda en scripts/backup.log (o BACKUP_LOG en .env). No usar >/dev/null.
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

resolve_env_file() {
	if [[ -n "${ENV_FILE:-}" ]]; then
		printf '%s' "$ENV_FILE"
		return
	fi
	if [[ -f "$SCRIPT_DIR/.env" ]]; then
		printf '%s' "$SCRIPT_DIR/.env"
		return
	fi
	printf '%s' "$PROJECT_DIR/.env"
}

ENV_FILE="$(resolve_env_file)"

apply_defaults() {
	: "${BACKUP_DIR:=/var/backups/crei}"
	: "${BACKUP_LOG:=${SCRIPT_DIR}/backup.log}"
	: "${BACKUP_RETENTION_DAYS:=14}"
	: "${MYSQL_CONTAINER:=crei-mysql}"
	: "${MYSQL_HOST:=127.0.0.1}"
	: "${MYSQL_PORT:=3306}"
	: "${MYSQL_DATABASE:=crei_integracion}"
	: "${MYSQL_USER:=crei}"
	: "${MYSQL_PASSWORD:=}"
	: "${MYSQL_ROOT_PASSWORD:=}"
}

log() {
	local line
	line="[$(date '+%Y-%m-%d %H:%M:%S')] $*"
	if [[ -n "${BACKUP_LOG:-}" ]]; then
		printf '%s\n' "$line" >> "$BACKUP_LOG"
	fi
	printf '%s\n' "$line" >&2
}

init_log() {
	if [[ -z "${BACKUP_LOG:-}" ]]; then
		return 0
	fi
	local log_dir
	log_dir="$(dirname "$BACKUP_LOG")"
	if [[ ! -d "$log_dir" ]]; then
		mkdir -p "$log_dir"
	fi
	touch "$BACKUP_LOG"
	chmod 600 "$BACKUP_LOG" 2>/dev/null || true
}

load_env() {
	if [[ ! -f "$ENV_FILE" ]]; then
		log "AVISO: no existe $ENV_FILE; se usan variables de entorno o valores por defecto."
		return 0
	fi

	while IFS= read -r line || [[ -n "$line" ]]; do
		[[ -z "$line" || "$line" =~ ^[[:space:]]*# ]] && continue
		if [[ "$line" =~ ^([A-Za-z_][A-Za-z0-9_]*)=(.*)$ ]]; then
			key="${BASH_REMATCH[1]}"
			# No pisar variables ya definidas en el entorno (p. ej. cron).
			if [[ -n "${!key+x}" ]]; then
				continue
			fi
			val="${BASH_REMATCH[2]}"
			val="${val%\"}"
			val="${val#\"}"
			val="${val%\'}"
			val="${val#\'}"
			printf -v "$key" '%s' "$val"
			export "$key"
		fi
	done < "$ENV_FILE"
}

use_docker() {
	command -v docker >/dev/null 2>&1 || return 1
	docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "$MYSQL_CONTAINER"
}

prepare_backup_dir() {
	if [[ ! -d "$BACKUP_DIR" ]]; then
		mkdir -p "$BACKUP_DIR"
	fi
	chmod 700 "$BACKUP_DIR"
}

purge_old_backups() {
	local days="${BACKUP_RETENTION_DAYS:-14}"
	[[ "$days" =~ ^[0-9]+$ ]] || days=14
	find "$BACKUP_DIR" -maxdepth 1 -type f -name "${MYSQL_DATABASE}_*.sql.gz" -mtime +"$days" -delete 2>/dev/null || true
}

run_mysqldump() {
	if use_docker; then
		local root_pass="${MYSQL_ROOT_PASSWORD:-}"
		if [[ -z "$root_pass" ]]; then
			log "ERROR: MYSQL_ROOT_PASSWORD vacío (necesario para backup vía Docker)."
			exit 1
		fi
		docker exec "$MYSQL_CONTAINER" mysqldump \
			-u root \
			-p"$root_pass" \
			--single-transaction \
			--routines \
			--events \
			--default-character-set=utf8mb4 \
			--hex-blob \
			"$MYSQL_DATABASE"
		return
	fi

	if [[ -z "$MYSQL_PASSWORD" ]]; then
		log "ERROR: MYSQL_PASSWORD vacío (backup sin Docker)."
		exit 1
	fi

	mysqldump \
		-h "$MYSQL_HOST" \
		-P "$MYSQL_PORT" \
		-u "$MYSQL_USER" \
		-p"$MYSQL_PASSWORD" \
		--single-transaction \
		--routines \
		--events \
		--default-character-set=utf8mb4 \
		--hex-blob \
		"$MYSQL_DATABASE"
}

main() {
	load_env
	apply_defaults
	init_log

	local timestamp backup_file tmp_file
	timestamp="$(date '+%Y%m%d_%H%M%S')"
	backup_file="$BACKUP_DIR/${MYSQL_DATABASE}_${timestamp}.sql.gz"
	tmp_file="${backup_file}.partial"

	prepare_backup_dir

	log "Iniciando backup de '$MYSQL_DATABASE' -> $backup_file"
	if use_docker; then
		log "Modo: Docker (contenedor $MYSQL_CONTAINER)"
	else
		log "Modo: mysqldump local ($MYSQL_HOST:$MYSQL_PORT)"
	fi

	if ! run_mysqldump 2>>"$BACKUP_LOG" | gzip -9 > "$tmp_file"; then
		rm -f "$tmp_file"
		log "ERROR: falló el backup."
		exit 1
	fi

	if [[ ! -s "$tmp_file" ]]; then
		rm -f "$tmp_file"
		log "ERROR: el archivo comprimido quedó vacío."
		exit 1
	fi

	mv "$tmp_file" "$backup_file"
	chmod 600 "$backup_file"

	purge_old_backups

	local size_kb
	size_kb="$(du -k "$backup_file" | awk '{print $1}')"
	log "OK: backup guardado (${size_kb} KB). Retención: ${BACKUP_RETENTION_DAYS} días."
}

main "$@"
