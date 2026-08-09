#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$project_dir"

mode="${1:-all}"

check_lint() {
    echo "Checking PHP syntax..."
    while IFS= read -r -d '' file; do
        php -d error_reporting='E_ALL & ~E_DEPRECATED' -l "$file" >/dev/null
    done < <(find public scripts -type f -name '*.php' -print0)
    echo "PHP syntax is valid."
}

check_data() {
    echo "Checking JSON data..."
    while IFS= read -r -d '' file; do
        php -r '
            $path = $argv[1];
            json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        ' "$file"
    done < <(find storage/data storage/cache -type f -name '*.json' -print0)
    echo "JSON data is valid."
}

case "$mode" in
    --lint-only) check_lint ;;
    --data-only) check_data ;;
    all) check_lint; check_data ;;
    *) echo "Usage: $0 [--lint-only|--data-only]" >&2; exit 2 ;;
esac
