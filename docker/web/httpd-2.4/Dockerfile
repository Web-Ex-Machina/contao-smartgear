ARG image
FROM $image AS web_base
ARG prj_name
ARG workdir_contao_public
ARG ssl_port

EXPOSE 80
EXPOSE 443
EXPOSE $ssl_port

# copy vhost
COPY ./my-vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf
COPY ./localhost.key /usr/local/apache2/ssl/localhost.key
COPY ./localhost.cert /usr/local/apache2/ssl/localhost.cert
COPY ./localhost.key /usr/local/apache2/ssl/localhost.loc.key
COPY ./localhost.cert /usr/local/apache2/ssl/localhost.loc.cert
COPY ./localhost.key /usr/local/apache2/ssl/www.localhost.loc.key
COPY ./localhost.cert /usr/local/apache2/ssl/www.localhost.loc.cert
COPY ./localhost.key /usr/local/apache2/ssl/server.key
COPY ./localhost.cert /usr/local/apache2/ssl/server.crt

RUN  sed -i 's~#Include conf/extra/httpd-vhosts.conf~Include conf/extra/httpd-vhosts.conf~g' /usr/local/apache2/conf/httpd.conf \
	&& sed -i -e 's/^#\(Include .*httpd-ssl.conf\)/\1/' /usr/local/apache2/conf/httpd.conf \
	&& sed -i -e 's/^#\(LoadModule .*mod_ssl.so\)/\1/' /usr/local/apache2/conf/httpd.conf \
	&& sed -i -e 's/^#\(LoadModule .*mod_socache_shmcb.so\)/\1/' /usr/local/apache2/conf/httpd.conf \
	&& sed -i "s~{{workdir_contao_public}}~$workdir_contao_public~g" /usr/local/apache2/conf/extra/httpd-vhosts.conf \
	&& sed -i "s~{{prj_name}}~$prj_name~g" /usr/local/apache2/conf/extra/httpd-vhosts.conf \
	&& sed -i "s~{{ssl_port}}~$ssl_port~g" /usr/local/apache2/conf/extra/httpd-vhosts.conf \
	&& sed -i "s~Listen 443~Listen 443\nListen $ssl_port~g" /usr/local/apache2/conf/extra/httpd-ssl.conf \
	&& sed -i "s~conf/server.crt~ssl/server.crt~g" /usr/local/apache2/conf/extra/httpd-ssl.conf \
	&& sed -i "s~conf/server.key~ssl/server.key~g" /usr/local/apache2/conf/extra/httpd-ssl.conf \
	# We give the www-data the same user UUID as ours
	&& usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

CMD apachectl -D FOREGROUND

FROM web_base AS web_test

# Here we can have additional work to be done specifically fot the test container