#!/bin/sh

# .fly/entrypoint.sh (Versão Final e Corrigida)
set -e

# Cria o diretório para o socket do PHP-FPM, se não existir.
mkdir -p /run/php

# Roda as migrations do banco de dados para garantir que a base esteja atualizada.
php artisan migrate --force

# Limpa e armazena em cache a configuração para otimizar a performance.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Inicia o supervisor, que gerencia os processos do Nginx e do PHP.
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf