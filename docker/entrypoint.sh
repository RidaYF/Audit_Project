#!/bin/sh
set -e

DB_PATH=/data/database.sqlite

# Fichier .env généré au premier démarrage du conteneur
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite\nDB_DATABASE=$DB_PATH|" .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

# Base SQLite persistée dans le volume /data, seedée uniquement à sa création
mkdir -p "$(dirname "$DB_PATH")"
FRESH_DB=0
if [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
    FRESH_DB=1
fi

php artisan migrate --force

if [ "$FRESH_DB" = "1" ]; then
    php artisan db:seed --class=DatabaseSeeder --force
    php artisan db:seed --class=IsoMasterDataSeeder --force
fi

[ -L public/storage ] || php artisan storage:link

exec "$@"
