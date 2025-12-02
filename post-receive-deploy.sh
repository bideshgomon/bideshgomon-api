#!/bin/bash
# Git post-receive hook helper to auto-deploy Bidesh Gomon after push to bare repo
# Usage:
# 1. On server: mkdir -p /var/repo/bideshgomon.git && cd /var/repo/bideshgomon.git && git init --bare
# 2. Copy this script into hooks/post-receive and chmod +x hooks/post-receive
# 3. Set WORK_TREE below to your live directory
# 4. Push from local: git remote add production ssh://user@server/var/repo/bideshgomon.git && git push production main
# 5. Hook will checkout and run deploy.sh

set -euo pipefail

REPO_DIR="$(pwd)"
WORK_TREE="/var/www/bideshgomon"
BRANCH="refs/heads/main"

while read oldrev newrev ref
do
  if [ "$ref" = "$BRANCH" ]; then
    echo "[post-receive] Deploying branch main to $WORK_TREE"
    # Checkout latest code
    git --work-tree="$WORK_TREE" --git-dir="$REPO_DIR" checkout -f main

    cd "$WORK_TREE"

    if [ -x "./deploy.sh" ]; then
      echo "Running deploy.sh (maintenance + build + migrate)"
      ./deploy.sh || { echo "deploy.sh failed"; exit 1; }
    else
      echo "deploy.sh missing - running minimal deployment"
      php artisan down || true
      composer install --optimize-autoloader --no-dev --no-interaction
      npm install --legacy-peer-deps --no-audit --no-fund
      npm run build
      php artisan migrate --force
      php artisan optimize
      php artisan up
    fi
    echo "Deployment complete for commit $newrev"
  fi
done
