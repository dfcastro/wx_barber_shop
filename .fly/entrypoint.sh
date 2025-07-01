#!/bin/sh

# .fly/entrypoint.sh
set -e

# Gera a chave da aplicação se não existir
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Roda as migrations do banco de dados
php artisan migrate --force

# Inicia o supervisor para rodar o Nginx e o PHP
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf