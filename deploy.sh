#!/bin/bash

set -euo pipefail

# path to the file with the configurations
# the config must follow the following pattern
# name  user@host   site path   composer path
siteconffile='deploy.conf'

##########################################
echoerr() {
    printf "%s\n" "$*" >&2
}

getConfigs() {
    local site="$1"

    if [ ! -r "$siteconffile" ]; then
        echoerr "ERROR: Unable to read config file '$siteconffile'"
    fi

    while read -r line; do
        local config=(${line})

        if [ "4" -ne "${#config[@]}" ]; then
            echoerr "ERROR: Invalid config for '${config[0]}'."
            echoerr "4 values expected but ${#config[@]} found."
            exit 2
        fi

        if [[ "${site}" == "${config[0]}" ]]; then
            echo "${line}"
            exit 0
        fi

        if [[ "-n" == "${site}" ]]; then
            echo "${config[0]}"
        fi
    done <<<$(sed '/^\s*#/ d; /^\s*$/ d;' $siteconffile)

    if [[ "-n" != "${site}" ]]; then
        echoerr "ERROR: no configuration for ${site}"
        exit 1
    fi
}

deploysingle() {
    local name="$1"
    local host="$2"
    local target="$3"
    local composer="$4"
    local quiet=$5
    local skipassets=$6
    local migrations=$7

    if [ -z "$host" ]; then
        echoerr "ERROR: Missing host argument"
    fi

    if [ -z "$target" ]; then
        echoerr "ERROR: Missing target argument"
    fi

    if [[ "0" == "$skipassets" ]]; then
        buildassets
    fi

    if [[ "0" == "$quiet" ]]; then
        sync "$host" "$target" $quiet

        read -p "The above files will be deployed for '$name'. Continue? [y/n] " -n 1
        echo

        if [[ ! "$REPLY" =~ ^[Yy]$ ]]; then
            return 1
        fi
    fi

    sync "$name" "$host" "$target" 1

    if [[ "0" != "$composer" ]]; then
        if ssh "$host" [ ! -x "\"$composer\"" ]; then
            echoerr "ERROR: Composer command on remote host not found or not executable."
            echoerr "Tried: $composer"
            exit 3
        fi

        ssh "$host" "\"$composer\" --working-dir=\"${target}\" install --no-dev"
    fi

    if [[ "0" != "$migrations" ]]; then
        ssh "$host" "cd \"${target}\" && php artisan migrate"
    fi

    ssh "$host" "cd \"${target}\" " \
                "&& php artisan cache:clear " \
                "&& php artisan config:clear " \
                "&& php artisan config:cache " \
                "&& php artisan route:clear " \
                "&& php artisan route:cache"
}

sync() {
    local name="$1"
    local host="$2"
    local target="$3"
    local quiet=$4
    local dry=
    local progress=

    if [[ "1" != "$quiet" ]]; then
        dry="vn"
    else
        progress="--info=progress2"
    fi

    echo "Starting upload for ${name}."

    rsync -rz${dry} \
        ${progress} \
        --delete \
        --exclude='/.docker' \
        --exclude='.git' \
        --exclude='/.idea' \
        --exclude='/docs' \
        --include='/public/css/***' \
        --include='/public/js/***' \
        --include='/public/fonts/***' \
        --include='/public/mix-manifest.json' \
        --include='/storage/app/base_logos/***' \
        --include='/storage/app/fonts/***' \
        --include='/storage/app/vector_logo_templates_indesign/***' \
        --exclude='/storage/app/logo_cache' \
        --exclude='/storage/app/logo_package_cache' \
        --exclude='/storage/temp' \
        --exclude='/tests' \
        --exclude='/.editorconfig' \
        --exclude='/.env*' \
        --exclude='/deploy.*' \
        --exclude='/docker-compose.yml' \
        --exclude='/install.sh' \
        --exclude='/migrate_*' \
        --exclude='/phpunit.xml' \
        --exclude='/productionSeeder.sql' \
        --exclude='/.htaccess' \
        --filter=':- .gitignore' \
        . "${host}:\"${target}\""

    echo "Upload for ${name} completed."
}

buildassets() {
    echo 'Start building assets'
    docker-compose up -d node
    docker exec imagery_node yarn production
    echo 'Assets built'
}

usage() {
    echo "Usage: deploy [ -c ] [ -q ] [ -s ] [ -m ] -a | ( names )"
    echo "  -c      run composer"
    echo "  -q      quiet"
    echo "  -s      skip building assets"
    echo "  -m      run database migrations"
    echo "  -a      deploy all sites in config. mutually exclusive with names"
    echo "  names   names of the sites to deploy. separate by a space. mutually exclusive with -a option."
    exit 2
}

##################### The program starts here

all=0
with_composer=0
with_migrations=0
quiet=0
skip=0
composer_path=0

while getopts 'acmqs' opt; do
    case "$opt" in
    "a") all=1 ;;
    "c") with_composer=1 ;;
    "m") with_migrations=1 ;;
    "q") quiet=1 ;;
    "s") skip=1 ;;
    *)
        usage
        exit 1
        ;;
    esac
done

if [[ "0" == "$all" ]]; then
    sites=("${@:$OPTIND}")
else
    sites=($(getConfigs -n))
fi

if [ -z "$sites" ]; then
    echoerr "ERROR: Missing argument. Provide a list of of sites to update."
    usage
    exit 1
fi

for site in "${sites[@]}"; do
    config=$(getConfigs "${site}")
    if [ "$?" -gt "0" ]; then
        exit 1
    fi

    config=($config)

    if [[ "0" != "${with_composer}" ]]; then
        composer_path="${config[3]}"
    fi

    deploysingle "${config[0]}" "${config[1]}" "${config[2]}" "${composer_path}" ${quiet} ${skip} ${with_migrations}

    skip=1
done
