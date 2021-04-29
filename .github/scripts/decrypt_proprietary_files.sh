#!/usr/bin/env bash

set -euo pipefail

cd $GITHUB_WORKSPACE

openssl enc \
    -d -aes256 \
    -md sha512 \
    -pbkdf2 -iter 100000 \
    -pass env:proprietary_key \
    -in proprietary.tar.gz.enc \
    | tar -xzv
