#!/bin/bash
mkdir -p ../${PRJ_NAME}/contao-manager
mkdir -p ../${PRJ_NAME}/config

# Not sure this line is still useful
cp ./system/config/localconfig.php ../${PRJ_NAME}/system/config/localconfig.php # webexmachina / azertyui
cp ./config/config.yml ../${PRJ_NAME}/config/config.yml
cp ./config/services.yml ../${PRJ_NAME}/config/services.yml
cp ./contao-manager/users.json ../${PRJ_NAME}/contao-manager/users.json # webexmachina / testtest
chown -R www-data:www-data ../${PRJ_NAME}/contao-manager
# We should create an admin user
php ../${PRJ_NAME}/vendor/bin/contao-console contao:user:create --username=admin --name=admin --email=admin@webexmachina.fr --password=adminadmin --language=fr --admin 
php contao-init.php # merge composer requierments
cd ${WORKDIR_CONTAO}
composer update
./vendor/bin/contao-console contao:migrate --no-interaction --with-deletes --no-backup
cd -