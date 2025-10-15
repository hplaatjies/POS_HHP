#!/usr/bin/env bash
# Helper script: run composer update inside a disposable PHP 7.4 container
# Usage: ./scripts/docker-composer-update.sh

set -euo pipefail
REPO_DIR=$(cd "$(dirname "$0")/.." && pwd)

docker run --rm -v "$REPO_DIR":/app -w /app php:7.4-cli bash -lc \
  "apt-get update -y && apt-get install -y unzip git && php -r 'copy(\"https://getcomposer.org/installer\", \"composer-setup.php\");' && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php && composer config platform.php 7.4 && composer update --with-all-dependencies --no-interaction"

echo "Done. If update succeeded, review composer.lock and vendor/ then commit changes."
