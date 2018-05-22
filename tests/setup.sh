#!/bin/bash

# Export the vars in .env into your shell:
export $(egrep -v '^#' .env | xargs)
DIR=${PWD}
WP=${DIR}/vendor/bin/wp
cd ${WP_ROOT_FOLDER}
mysql -u${DB_USER} -p${DB_PASSWORD} -h${DB_HOST} -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME};"

${WP} core install --url=${WP_URL} --title=DevPress --admin_user=${ADMIN_USERNAME} --admin_password=${ADMIN_PASSWORD} --admin_email=${ADMIN_EMAIL} --skip-email
sed -i "/define( 'DB_COLLATE', '' );/ a\if(!defined('DISABLE_WP_CRON')) define('DISABLE_WP_CRON', true);" wp-config.php
${WP} plugin activate menu-image
if [ -z "${DIR}/tests/_data/dump.sql" ]; then
    rm ${DIR}/tests/_data/dump.sql
fi;
${WP} db export ${DIR}/tests/_data/dump.sql