#!/bin/sh

set -euo pipefail

cd $GITHUB_WORKSPACE

echo "DEBUG: ${PROPRIETARY_KEY:0:2}****${PROPRIETARY_KEY:31}--"

openssl enc \
    -d -aes256 \
    -md sha512 \
    -pbkdf2 -iter 100000 \
    -pass env:PROPRIETARY_KEY \
    -in proprietary.tar.gz.enc \
    | tar -xzv
