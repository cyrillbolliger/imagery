#!/bin/bash

set -euo pipefail

openssl enc \
    -d -aes256 \
    -md sha512 \
    -pbkdf2 -iter 100000 \
    -pass env:PROPRIETARY_KEY \
    -in proprietary.tar.gz.enc \
    | tar -xzv
