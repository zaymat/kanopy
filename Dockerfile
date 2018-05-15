FROM php:latest
COPY . /usr/src/kanopy
WORKDIR /usr/src/kanopy
CMD [ "php", "./index.php" ]