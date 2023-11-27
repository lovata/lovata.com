#!/bin/bash

if [ ! -f /etc/nginx/ssl/default.crt ]; then
    openssl genrsa -out "/etc/nginx/ssl/default.key" 2048
    openssl req -new -key "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.csr" -subj "/C=BY/ST=Minsk/L=Minsk/O=2doc/OU=Development/CN=2doc.loc/emailAddress=a.kharanenka@2doc.by"
    openssl x509 -req -days 365 -in "/etc/nginx/ssl/default.csr" -signkey "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.crt"
fi

# Start crond in background
crond -l 2 -b

# Start nginx in foreground
nginx
