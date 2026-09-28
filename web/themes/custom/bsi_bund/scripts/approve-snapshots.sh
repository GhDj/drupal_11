#!/bin/bash

# Approve visual snapshots by copying received images to baselines
# Usage: npm run test:visual:approve

SNAPSHOTS_DIR="__snapshots__"
RECEIVED_DIR="$SNAPSHOTS_DIR/__received__"
DIFF_DIR="$SNAPSHOTS_DIR/__diff_output__"

# Check if received directory exists and has files
if [ ! -d "$RECEIVED_DIR" ] || [ -z "$(ls -A "$RECEIVED_DIR" 2>/dev/null)" ]; then
  echo "No received snapshots to approve."
  echo "Run 'npm run test:storybook' first to generate snapshots."
  exit 0
fi

# Count files to approve
count=$(ls -1 "$RECEIVED_DIR"/*-received.png 2>/dev/null | wc -l)
echo "Approving $count snapshot(s)..."

# Move received files to snapshots directory, removing "-received" suffix
for f in "$RECEIVED_DIR"/*-received.png; do
  if [ -f "$f" ]; then
    basename=$(basename "$f" -received.png)
    mv "$f" "$SNAPSHOTS_DIR/$basename.png"
    echo "  Approved: $basename.png"
  fi
done

# Clean up
rm -rf "$RECEIVED_DIR" "$DIFF_DIR"

echo "Done! $count snapshot(s) approved."
