FROM minds/php:latest

RUN apk update && apk add --no-cache git nodejs

COPY containers/installer/install.sh install.sh
RUN chmod a+x install.sh

CMD ["/bin/sh", "-c", "./install.sh 2>&1 | tee /var/log/installer.log"]
