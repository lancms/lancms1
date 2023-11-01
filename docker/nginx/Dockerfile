FROM docker.io/library/nginx:alpine
RUN rm /etc/nginx/conf.d/default.conf
COPY ./site.conf /etc/nginx/conf.d/site.conf
# RUN apk --update --no-cache add bash
