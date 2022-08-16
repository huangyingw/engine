FROM minds/php:8.0

RUN apk update && apk add  --update git

COPY containers/installer/install.sh install.sh

ENTRYPOINT [ "sh", "./install.sh" ]
