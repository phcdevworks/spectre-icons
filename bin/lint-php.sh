#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd -- "$(dirname "$0")/.." && pwd)"

echo "Running PHP lint across project..."

find "$ROOT_DIR" -type f -name '*.php' \
	! -path "$ROOT_DIR/vendor/*" \
	! -path "$ROOT_DIR/node_modules/*" \
	-print -exec php -l {} \;

echo "PHP lint completed successfully."
