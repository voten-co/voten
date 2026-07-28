#!/bin/sh

set -eu
umask 077

deploy_root=${VOTEN_DEMO_ROOT:-/opt/voten-demo}
project_dir="$deploy_root/app"
env_file="$deploy_root/.env"
backup_dir="$deploy_root/backups"
compose_file="$project_dir/docker-compose.demo.yml"
timestamp=$(date -u +%Y%m%dT%H%M%SZ)
database_archive="$backup_dir/voten-database-$timestamp.sql.gz"
storage_archive="$backup_dir/voten-storage-$timestamp.tar.gz"

if [ ! -f "$env_file" ] || [ ! -f "$compose_file" ]; then
    echo "Voten deployment files were not found under $deploy_root." >&2
    exit 1
fi

compose()
{
    docker compose \
        --project-directory "$project_dir" \
        --env-file "$env_file" \
        --file "$compose_file" \
        "$@"
}

mkdir -p "$backup_dir"

database_tmp="$database_archive.tmp"
storage_tmp="$storage_archive.tmp"
trap 'rm -f "$database_tmp" "$storage_tmp"' EXIT

compose exec -T mysql sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqldump --single-transaction --routines --events -uroot "$MYSQL_DATABASE"' \
    | gzip -9 > "$database_tmp"
gzip -t "$database_tmp"
mv "$database_tmp" "$database_archive"

app_container=$(compose ps -q app)
if [ -z "$app_container" ]; then
    echo "The Voten app container is not available; storage cannot be located." >&2
    exit 1
fi

storage_volume=$(docker inspect --format \
    '{{range .Mounts}}{{if eq .Destination "/var/www/html/storage"}}{{.Name}}{{end}}{{end}}' \
    "$app_container")
if [ -z "$storage_volume" ]; then
    echo "The Voten storage volume could not be located." >&2
    exit 1
fi

docker run --rm \
    --volume "$storage_volume:/storage:ro" \
    alpine:3.20 \
    tar -czf - -C /storage . > "$storage_tmp"
gzip -t "$storage_tmp"
mv "$storage_tmp" "$storage_archive"

find "$backup_dir" -type f \
    \( -name 'voten-database-*.sql.gz' -o -name 'voten-storage-*.tar.gz' \) \
    -mtime +7 -delete

trap - EXIT
