# Deploying the Voten demo

This deployment is intended for demonstrations of the archived Voten project. It runs the Laravel application, MySQL, Redis, the queue worker, the scheduler, and Caddy in Docker. Caddy obtains and renews the HTTPS certificate automatically.

The demo seeder is deterministic and safe to rerun. A fresh database receives sample users, channels, discussions, comments, subscriptions, roles, votes, and activity records. An existing demo database is repaired without duplicating that content.

## Requirements

- A Linux server with at least 2 GB of RAM, 2 CPU cores, and 10 GB of free disk space
- Docker Engine and the Docker Compose plugin
- Git and OpenSSL
- A domain with an `A` record pointing to the server
- Inbound TCP ports 22, 80, and 443; UDP 443 is optional for HTTP/3

Use Docker's current [Ubuntu installation guide](https://docs.docker.com/engine/install/ubuntu/) rather than distribution packages that may ship an outdated Compose version.

If Cloudflare proxies the domain, use **Full (strict)** SSL mode. Do not use Flexible mode, which causes redirect loops.

## Quick installation

The recommended layout keeps secrets outside the Git checkout:

```text
/opt/voten-demo/
├── .env
├── app/
└── backups/
```

Point the domain to the server first, then run:

```bash
sudo mkdir -p /opt/voten-demo
sudo git clone https://github.com/voten-co/voten.git /opt/voten-demo/app
cd /opt/voten-demo/app
sudo ./docker/deploy.sh voten.example.com
```

Replace `voten.example.com` with the real hostname. The installer:

1. generates the application key, database passwords, and demo passwords;
2. stores them in `/opt/voten-demo/.env` with mode `0600`;
3. builds the pinned legacy PHP and JavaScript application;
4. starts MySQL and Redis;
5. runs all migrations and the demo seeder;
6. starts the web app, queue, scheduler, and Caddy;
7. installs the daily backup and log-rotation configuration.

The administrator username is `admin`. Read `DEMO_ADMIN_PASSWORD` and `DEMO_USER_PASSWORD` from `/opt/voten-demo/.env` over SSH; do not copy that file into Git or expose it in logs.

The initial build compiles an old dependency tree and can take several minutes.

## Verify the deployment

From `/opt/voten-demo/app`:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml ps
curl -fsS https://voten.example.com/ >/dev/null
curl -fsS 'https://voten.example.com/api/guest/feed?page=1'
```

The `app`, `mysql`, and `redis` services should report healthy. The guest feed should return demo submissions instead of an empty `data` array.

Inspect recent application output:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml \
  logs --since=10m --no-color app queue scheduler
```

## Manual installation and recovery

If the installer cannot finish, its steps can be run individually:

```bash
cd /opt/voten-demo/app
sudo cp docker/demo.env.example ../.env
sudo chmod 600 ../.env
sudo editor ../.env
```

Replace every `voten.example.com` value and every `__PLACEHOLDER__`. Generate values with:

```bash
openssl rand -hex 24
openssl rand -base64 32
```

Prefix the base64 application key with `base64:`. Then run:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml build app
docker compose --env-file ../.env -f docker-compose.demo.yml up -d mysql redis
docker compose --env-file ../.env -f docker-compose.demo.yml run --rm app php artisan migrate --force
docker compose --env-file ../.env -f docker-compose.demo.yml run --rm app php artisan db:seed --force
docker compose --env-file ../.env -f docker-compose.demo.yml up -d
docker compose --env-file ../.env -f docker-compose.demo.yml exec -T app php artisan config:clear
docker compose --env-file ../.env -f docker-compose.demo.yml exec -T app php artisan view:clear
```

## Updating an existing installation

Create a backup before every update:

```bash
sudo /opt/voten-demo/backup.sh
cd /opt/voten-demo/app
git pull --ff-only
sudo ./docker/deploy.sh voten.example.com
```

The installer preserves an existing `.env`, applies new migrations, reruns the idempotent demo seeder, and recreates services from the rebuilt image.

## Backups

The installer adds a daily backup at 03:17 UTC with seven-day retention. It saves:

- compressed MySQL dumps as `/opt/voten-demo/backups/voten-database-*.sql.gz`;
- compressed uploaded files as `/opt/voten-demo/backups/voten-storage-*.tar.gz`.

Run and validate a backup immediately after deployment:

```bash
sudo /opt/voten-demo/backup.sh
gzip -t /opt/voten-demo/backups/voten-database-*.sql.gz
gzip -t /opt/voten-demo/backups/voten-storage-*.tar.gz
```

Copy backups to another machine or object-storage provider. Files kept only on the application server are not sufficient disaster recovery.

## Restoring a backup

The following operation replaces the current demo database and uploaded files. Confirm the archive names before running it.

Start the database services and create the app container:

```bash
cd /opt/voten-demo/app
docker compose --env-file ../.env -f docker-compose.demo.yml up -d mysql redis
docker compose --env-file ../.env -f docker-compose.demo.yml create app
```

Restore MySQL:

```bash
gzip -dc /opt/voten-demo/backups/voten-database-YYYYMMDDTHHMMSSZ.sql.gz |
  docker compose --env-file ../.env -f docker-compose.demo.yml exec -T mysql sh -c \
  'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot "$MYSQL_DATABASE"'
```

Locate and restore the named storage volume:

```bash
app_container=$(docker compose --env-file ../.env -f docker-compose.demo.yml ps -aq app)
storage_volume=$(docker inspect --format \
  '{{range .Mounts}}{{if eq .Destination "/var/www/html/storage"}}{{.Name}}{{end}}{{end}}' \
  "$app_container")

docker run --rm \
  --volume "$storage_volume:/storage" \
  --volume /opt/voten-demo/backups:/backups:ro \
  alpine:3.20 sh -c \
  'find /storage -mindepth 1 -maxdepth 1 -exec rm -rf -- {} + &&
   tar -xzf /backups/voten-storage-YYYYMMDDTHHMMSSZ.tar.gz -C /storage'
```

Then start and verify everything:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml up -d
docker compose --env-file ../.env -f docker-compose.demo.yml exec -T app php artisan config:clear
docker compose --env-file ../.env -f docker-compose.demo.yml ps
```

## Stopping or removing the demo

Stop the services while preserving containers and data:

```bash
cd /opt/voten-demo/app
docker compose --env-file ../.env -f docker-compose.demo.yml stop
```

Remove containers and the network while preserving database, Redis, uploaded files, and HTTPS certificates:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml down
```

To start it again:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml up -d
```

Permanent removal is destructive. After making and copying a verified backup, remove the named volumes with:

```bash
docker compose --env-file ../.env -f docker-compose.demo.yml down --volumes
```

The last command permanently deletes the database, Redis data, uploads, and Caddy certificate data.
