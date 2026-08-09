#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$project_dir"

if [[ ! -f .env ]]; then
    ./scripts/setup.sh
fi

set -a
# shellcheck disable=SC1091
source .env
set +a

app_host="${APP_HOST:-127.0.0.1}"
app_port="${APP_PORT:-8080}"

echo "Night Sky Atlas: http://${app_host}:${app_port}"
echo "Admin: http://${app_host}:${app_port}${ADMIN_ROUTE:-/hidden-admin}/login"
exec php -d display_errors=1 -d error_reporting=E_ALL -S "${app_host}:${app_port}" -t public public/index.php
