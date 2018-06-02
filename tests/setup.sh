#!/bin/bash

# Export the vars in .env into your shell:
export $(egrep -v '^#' .env | xargs)
DIR=${PWD}
WP=${DIR}/vendor/bin/wp
cd ${WP_ROOT_FOLDER}

# Create database and drop previous one.
mysql -u${DB_USER} $(if [ ! -z DB_PASSWORD ]; then echo "-p${DB_PASSWORD}"; fi)  -h${DB_HOST} -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME};"

# Install and configure wordpress.
${WP} config create --dbname=${DB_NAME} --dbuser=${DB_USER} --dbhost=${DB_HOST} --dbprefix=${TABLE_PREFIX}
${WP} core install --url=${WP_URL} --title=DevPress --admin_user=${ADMIN_USERNAME} --admin_password=${ADMIN_PASSWORD} --admin_email=${ADMIN_EMAIL} --skip-email

# Add setting to disable cron.
if ! grep -q DISABLE_WP_CRON wp-config.php; then
    sed -i "/define( 'DB_COLLATE', '' );/ a\if(!defined('DISABLE_WP_CRON')) define('DISABLE_WP_CRON', true);" wp-config.php
fi

# Link plugin to wordpress plugins subfolder and activate it.
rm -rf ${WP_ROOT_FOLDER}/wp-content/plugins/menu-image
ln -s ${DIR} ${WP_ROOT_FOLDER}/wp-content/plugins/menu-image
${WP} plugin activate menu-image

# Dump database for codeception.
if [ -z "${DIR}/tests/_data/dump.sql" ]; then
    rm ${DIR}/tests/_data/dump.sql
fi;
${WP} db export ${DIR}/tests/_data/dump.sql
