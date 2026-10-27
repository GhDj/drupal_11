#!/bin/bash
# Führt Storybook-Snapshot-Tests in einem Docker-Container aus, um
# plattformunabhängig identische Screenshots zu erzeugen.
# Voraussetzung: Docker muss laufen, 'npm run build-storybook' muss
# vorher auf dem Host ausgeführt worden sein.
set -e

cd "$(dirname "$0")/.."

if [ ! -d "storybook-static" ]; then
  echo "Error: storybook-static/ fehlt. Zuerst 'npm run build-storybook' ausführen." >&2
  exit 1
fi

echo "Starte Snapshot-Tests im Docker-Container..."
echo "Beim ersten Aufruf dauert das Image-Download ca. 1,5 GB."

docker run --rm \
  --platform linux/amd64 \
  --user "$(id -u):$(id -g)" \
  -v "$(pwd):/work" \
  -w /work \
  -e TZ=UTC \
  -e HOME=/tmp \
  mcr.microsoft.com/playwright:v1.61.1-noble \
  bash -c '
    python3 -m http.server 6006 --directory storybook-static --bind 127.0.0.1 &>/dev/null &
    SERVER_PID=$!
    sleep 2
    node_modules/.bin/test-storybook --testTimeout=60000 --url http://localhost:6006
    EXIT_CODE=$?
    kill $SERVER_PID 2>/dev/null || true
    exit $EXIT_CODE
  '
