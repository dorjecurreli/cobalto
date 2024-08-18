#!/bin/bash

# Encode the first file
base64 -i deployment/.env.production -o deployment/.env.production.encoded

# Check if the first encoding was successful
if [ $? -ne 0 ]; then
    echo "Failed to encode deployment/.env.production"
    exit 1
fi

# Encode the second file
base64 -i app/prod.env -o app/prod.env.encoded

# Check if the second encoding was successful
if [ $? -ne 0 ]; then
    echo "Failed to encode app/prod.env"
    exit 1
fi

echo "Files have been encoded successfully."
