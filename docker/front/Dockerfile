FROM node:24-alpine

WORKDIR /app

COPY docker/front/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 5173

ENTRYPOINT ["/bin/sh", "/entrypoint.sh"]
