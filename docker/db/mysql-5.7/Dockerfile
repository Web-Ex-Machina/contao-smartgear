ARG image
FROM $image AS db_base

EXPOSE 3306

CMD ["mysqld"]

FROM db_base AS db_test

# Here we can have additional work to be done specifically fot the test container
