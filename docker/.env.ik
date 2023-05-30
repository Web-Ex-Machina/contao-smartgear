# Project name
COMPOSE_PROJECT_NAME=smartgear
COMPOSE_PROJECT_NAME_SHORT=sm
PRJ_NAME='smartgear'
BUNDLE_NAME='contao-smartgear'

# Database settings
DB_HOST='db' # database service name in docker-compose file
DB_DATABASE='ms_test'
DB_USER='smartgear'
DB_PASSWORD='root'
DB_ROOT_PASSWORD='root'
# DATABASE_URL="mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:3306/${DB_DATABASE}" #Yes, 3306, because inside docker network, the internal ports are useable, not the exposed ones

# Ports settings
PHP_FPM_EXPOSED_PORT=9002
DB_EXPOSED_PORT=3309
WEBSERVER_EXPOSED_PORT=8004
WEBSERVER_EXPOSED_PORT_SSL=8005

PHP_FPM_EXPOSED_PORT_TEST=9003
DB_EXPOSED_PORT_TEST=3310
WEBSERVER_EXPOSED_PORT_TEST=8006
WEBSERVER_EXPOSED_PORT_SSL_TEST=8007

# Directories settings
WORKDIR_BASE='/usr/local/apache2/htdocs'
WORKDIR_INIT="${WORKDIR_BASE}/init"
WORKDIR_CONTAO="${WORKDIR_BASE}/${PRJ_NAME}"
WORKDIR_CONTAO_PUBLIC="${WORKDIR_CONTAO}/public"
# WORKDIR_BUNDLE="${WORKDIR_BASE}/${BUNDLE_NAME}"
WORKDIR_BUNDLE="${WORKDIR_BASE}/${PRJ_NAME}/vendor/webexmachina/${BUNDLE_NAME}"
WORKDIR_SIMLINK_BASE="${WORKDIR_BASE}/${PRJ_NAME}/vendor/webexmachina"
WORKDIR_BUNDLE_END_SLASH="${WORKDIR_BUNDLE}/"

# PHP settings (those are our main host default values)
PHP_INI_MAX_EXECUTION_TIME=60
PHP_INI_MEMORY_LIMIT=640M
PHP_INI_DISABLE_FUNCTIONS=set_time_limit
PHP_INI_POST_MAX_SIZE=8M
PHP_INI_UPLOAD_MAX_FILESIZE=2M
PHP_INI_MAX_INPUT_VARS=1000