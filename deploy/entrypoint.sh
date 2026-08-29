#!/bin/sh
set -eu

if [ -n "${MYSQLHOST:-}" ] && [ -n "${MYSQLDATABASE:-}" ]; then
  php /var/www/html/deploy/init-db.php
fi

exec "$@"
