#!/bin/sh

set -eu
umask 077

if [ "$(id -u)" -ne 0 ]; then
    echo "Run this installer as root." >&2
    exit 1
fi

domain=${1:-}
deploy_root=${VOTEN_DEMO_ROOT:-/opt/voten-demo}
project_dir=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
env_file="$deploy_root/.env"
compose_file="$project_dir/docker-compose.demo.yml"

if [ -z "$domain" ]; then
    echo "Usage: sudo ./docker/deploy.sh voten.example.com" >&2
    exit 1
fi

case "$domain" in
    *[!A-Za-z0-9.-]* | .* | *..* | *.)
        echo "The domain name is not valid: $domain" >&2
        exit 1
        ;;
esac

for command_name in docker openssl sed; do
    if ! command -v "$command_name" >/dev/null 2>&1; then
        echo "Required command is missing: $command_name" >&2
        exit 1
    fi
done

if ! docker compose version >/dev/null 2>&1; then
    echo "The Docker Compose plugin is required." >&2
    exit 1
fi

mkdir -p "$deploy_root"

if [ ! -f "$env_file" ]; then
    app_key="base64:$(openssl rand -base64 32)"
    db_password=$(openssl rand -hex 24)
    mysql_root_password=$(openssl rand -hex 24)
    demo_admin_password=$(openssl rand -hex 12)
    demo_user_password=$(openssl rand -hex 12)

    sed \
        -e "s|voten.example.com|$domain|g" \
        -e "s|__APP_KEY__|$app_key|" \
        -e "s|__DB_PASSWORD__|$db_password|" \
        -e "s|__MYSQL_ROOT_PASSWORD__|$mysql_root_password|" \
        -e "s|__DEMO_ADMIN_PASSWORD__|$demo_admin_password|" \
        -e "s|__DEMO_USER_PASSWORD__|$demo_user_password|" \
        "$project_dir/docker/demo.env.example" > "$env_file"
    chmod 600 "$env_file"
else
    configured_domain=$(sed -n 's/^DEMO_DOMAIN=//p' "$env_file")
    if [ -z "$configured_domain" ]; then
        configured_url=$(sed -n 's/^APP_URL=//p' "$env_file")
        if [ "$configured_url" != "https://$domain" ]; then
            echo "The existing environment uses $configured_url, not https://$domain." >&2
            echo "Update $env_file intentionally before changing domains." >&2
            exit 1
        fi

        printf '\nDEMO_DOMAIN=%s\n' "$domain" >> "$env_file"
        configured_domain=$domain
    fi

    if [ "$configured_domain" != "$domain" ]; then
        echo "The existing environment is configured for $configured_domain, not $domain." >&2
        echo "Update $env_file intentionally before changing domains." >&2
        exit 1
    fi
fi

compose()
{
    docker compose \
        --project-directory "$project_dir" \
        --env-file "$env_file" \
        --file "$compose_file" \
        "$@"
}

compose build app
compose up -d mysql redis

attempt=0
until compose exec -T mysql sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqladmin ping -h 127.0.0.1 -uroot --silent' \
    >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 30 ]; then
        echo "MySQL did not become healthy in time." >&2
        exit 1
    fi
    sleep 2
done

compose run --rm app php artisan migrate --force
compose run --rm app php artisan db:seed --force
compose up -d
compose exec -T app php artisan config:clear
compose exec -T app php artisan view:clear

install -m 0700 "$project_dir/docker/backup.sh" "$deploy_root/backup.sh"
printf '17 3 * * * root %s/backup.sh >> /var/log/voten-demo-backup.log 2>&1\n' \
    "$deploy_root" > /etc/cron.d/voten-demo-backup
chmod 0644 /etc/cron.d/voten-demo-backup
install -m 0644 "$project_dir/docker/voten-demo-backup.logrotate" /etc/logrotate.d/voten-demo-backup

echo
echo "Voten is running at https://$domain"
echo "Demo administrator username: admin"
echo "Passwords and generated secrets are stored in $env_file"
echo "Run '$deploy_root/backup.sh' after the first health check."
