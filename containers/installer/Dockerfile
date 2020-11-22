FROM minds/php:7.3

RUN apk update && apk add  --update git

COPY containers/installer/install.sh install.sh

ENTRYPOINT [ "sh", "./install.sh" ]
