#!/bin/sh
set -eu

if { [ -n "${MYSQLHOST:-}" ] && [ -n "${MYSQLDATABASE:-}" ]; } || { [ -n "${AA_DB_HOST:-}" ] && [ -n "${AA_DB_NAME:-}" ]; }; then
  php /var/www/html/deploy/init-db.php
fi

exec "$@"
