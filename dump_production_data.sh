#!/bin/bash

# Dump production database (only data tables, exclude wilayah)
# Run this on production server

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DUMP_FILE="kitorangpeduli_data_${TIMESTAMP}.sql"

echo "Dumping production data..."
echo "Output file: $DUMP_FILE"

# Dump only data tables (exclude wilayah tables which we already have)
pg_dump -h localhost -U postgres -d kitorangpeduli_db \
  --data-only \
  --exclude-table=provinces \
  --exclude-table=regencies \
  --exclude-table=districts \
  --exclude-table=villages \
  > "$DUMP_FILE"

if [ $? -eq 0 ]; then
    echo "✓ Dump successful: $DUMP_FILE"
    echo "File size: $(ls -lh $DUMP_FILE | awk '{print $5}')"
    echo ""
    echo "Next steps:"
    echo "1. Download this file to your local machine"
    echo "2. Run: psql -U postgres -d kitorangpeduli_db < $DUMP_FILE"
else
    echo "✗ Dump failed!"
    exit 1
fi
