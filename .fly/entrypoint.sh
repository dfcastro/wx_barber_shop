#!/bin/sh

# .fly/entrypoint.sh (Versão Final)
set -e

# Este script não precisa mais do .env.example.
# A Fly.io injeta os segredos (como DATABASE_URL e APP_KEY)
# diretamente como variáveis de ambiente, que o Laravel lê automaticamente.

# Roda as migrations do banco de dados para garantir que a base esteja atualizada.
# O --force é necessário para rodar em ambiente de produção.
php artisan migrate --force

# Limpa e armazena em cache a configuração para otimizar a performance.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Inicia o supervisor, que gerencia os processos do Nginx e do PHP.
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf