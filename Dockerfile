# Use a imagem oficial e otimizada da Fly.io para Laravel com PHP 8.2
FROM flyio/laravel:8.2

# Copia os arquivos de configuração do Nginx e PHP
COPY .fly/nginx.conf /etc/nginx/sites-enabled/default
COPY .fly/php.ini /etc/php/8.2/cli/conf.d/99-sail.ini

# Copia o código da sua aplicação para dentro do servidor
COPY . /var/www/html