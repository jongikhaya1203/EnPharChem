#!/bin/bash
# EnPharChem container entrypoint: wait for MySQL, import schema on first boot,
# then hand off to Apache. Works for both the on-prem compose stack and cloud
# (managed DB) deployments.
set -e

APP_DIR=/var/www/html/enpharchem
: "${DB_HOST:=db}"
: "${DB_PORT:=3306}"
: "${DB_NAME:=enpharchem}"
: "${DB_USER:=root}"
: "${DB_PASS:=}"

# MYSQL_PWD lets the mysql client read an (even empty) password without prompting
export MYSQL_PWD="$DB_PASS"
MYSQL="mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER}"

echo "[entrypoint] waiting for MySQL at ${DB_HOST}:${DB_PORT} ..."
for i in $(seq 1 60); do
  if mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USER}" --silent 2>/dev/null; then
    echo "[entrypoint] MySQL is up"
    break
  fi
  sleep 2
  if [ "$i" = "60" ]; then echo "[entrypoint] WARNING: MySQL not reachable, continuing anyway"; fi
done

# Ensure database exists (harmless if the managed DB already created it)
$MYSQL -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true

# Import schema only on a fresh database (detected by absence of the users table)
HAS_USERS=$($MYSQL -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='users';" 2>/dev/null || echo 0)
if [ "$HAS_USERS" = "0" ]; then
  echo "[entrypoint] fresh database — importing schema"
  for f in schema.sql control_panel_tables.sql training_assessment_tables.sql licensing_tables.sql; do
    if [ -f "${APP_DIR}/database/${f}" ]; then
      echo "  -> ${f}"
      $MYSQL "${DB_NAME}" < "${APP_DIR}/database/${f}" || echo "     (warnings ignored)"
    fi
  done
else
  echo "[entrypoint] existing database detected — skipping schema import"
fi

echo "[entrypoint] license enforcement: ${FLOWSHEET_LICENSE_ENFORCE:-true}"
echo "[entrypoint] starting Apache"
exec "$@"
