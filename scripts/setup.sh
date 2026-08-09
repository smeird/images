#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$project_dir"

if ! command -v php >/dev/null 2>&1; then
    echo "PHP 7.4 or newer is required." >&2
    exit 1
fi

php_version="$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')"
if ! php -r 'exit(PHP_VERSION_ID >= 70400 ? 0 : 1);'; then
    echo "PHP 7.4 or newer is required; found PHP $php_version." >&2
    exit 1
fi

required_modules=(fileinfo gd json mbstring openssl session)
for module in "${required_modules[@]}"; do
    if ! php -r 'exit(extension_loaded($argv[1]) ? 0 : 1);' "$module"; then
        echo "Missing required PHP extension: $module" >&2
        exit 1
    fi
done

mkdir -p storage/logs storage/sessions storage/uploads/tmp storage/images/original storage/images/thumbs
chmod u+rwx storage storage/logs storage/sessions storage/uploads storage/uploads/tmp storage/images storage/images/original storage/images/thumbs

if [[ ! -f .env ]]; then
    cp .env.example .env
    echo "Created .env from .env.example."
else
    echo "Kept existing .env."
fi

echo "Development environment ready (PHP $php_version). Run: make dev"
