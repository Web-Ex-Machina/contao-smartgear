ARG image
FROM $image AS db_base

EXPOSE 3306

CMD ["mysqld"]

FROM db_base AS db_test

FROM db_base AS db_dev
