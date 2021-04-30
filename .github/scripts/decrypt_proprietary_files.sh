#!/usr/bin/env bash

set -euo pipefail

cd $GITHUB_WORKSPACE

echo "DEBUG: ${proprietary_key:0:2}****${proprietary_key:31}--"

openssl enc \
    -d -aes256 \
    -md sha512 \
    -pbkdf2 -iter 100000 \
    -pass env:proprietary_key \
    -in proprietary.tar.gz.enc \
    | tar -xzv
