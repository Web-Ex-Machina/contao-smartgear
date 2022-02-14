#!/bin/bash
mkdir -p ../${PRJ_NAME}/contao-manager
mkdir -p ../${PRJ_NAME}/templates/smartgear
mkdir -p ../${PRJ_NAME}/config

cp ./system/config/localconfig.php ../${PRJ_NAME}/system/config/localconfig.php # webexmachina / azertyui
cp ./config/config.yml ../${PRJ_NAME}/config/config.yml
cp -r ./templates/smartgear/. ../${PRJ_NAME}/templates/smartgear/
chown -R www-data:www-data ../${PRJ_NAME}/contao-manager
# We should create an admin user
php contao-init.php # creates user + merge composer requierments
cd ${WORKDIR_CONTAO}
composer update
# cp ${WORKDIR_BUNDLE}/src/Migrations/_results.log ${WORKDIR_BUNDLE}/src/Migrations/_results_local.log
# cat ${WORKDIR_BUNDLE}/src/Migrations/_results.log
./vendor/bin/contao-console contao:migrate --no-interaction --with-deletes
# rm ${WORKDIR_BUNDLE}/src/Migrations/_results.log
# mv ${WORKDIR_BUNDLE}/src/Migrations/_results_local.log ${WORKDIR_BUNDLE}/src/Migrations/_results.log
cd -