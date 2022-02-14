ARG image
FROM $image AS web_base
ARG prj_name
ARG workdir_contao_web

EXPOSE 80

# allow vhost
RUN sed -i 's~#Include conf/extra/httpd-vhosts.conf~Include conf/extra/httpd-vhosts.conf~g' /usr/local/apache2/conf/httpd.conf

# copy vhost
COPY ./my-vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf
COPY ./localhost.key /usr/local/apache2/ssl/localhost.key
COPY ./localhost.cert /usr/local/apache2/ssl/localhost.cert
RUN sed -i "s~{{workdir_contao_web}}~$workdir_contao_web~g" /usr/local/apache2/conf/extra/httpd-vhosts.conf && \
	sed -i "s~{{prj_name}}~$prj_name~g" /usr/local/apache2/conf/extra/httpd-vhosts.conf \
	# We give the www-data the same user UUID as ours
	&& usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

CMD apachectl -D FOREGROUND

FROM web_base AS web_test

# Here we can have additional work to be done specifically fot the test container