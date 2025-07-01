# Usa uma imagem base do Ubuntu, nos dando controle total
# ATENÇÃO: A primeira linha pode mudar dependendo do seu fly.toml. 
# Se seu fly.toml tem uma seção [build] com um "builder", apague essa seção.
FROM ubuntu:jammy

# Instala dependências do sistema e PHP 8.2 com extensões
RUN apt-get update && apt-get install -y \
    software-properties-common curl \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update && apt-get install -y \
    php8.2 \
    php8.2-cli \
    php8.2-mysql \
    php8.2-xml \
    php8.2-mbstring \
    php8.2-curl \
    nginx \
    supervisor

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala Node.js e NPM
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get update && apt-get install -y nodejs

# Define o diretório de trabalho dentro do contêiner
WORKDIR /var/www/html

# Copia os arquivos da sua aplicação para o contêiner
COPY . .

# Instala as dependências do PHP
RUN composer install --optimize-autoloader --no-dev

# A SOLUÇÃO: Usa 'npm ci' para instalar as dependências de front-end de forma confiável
RUN npm ci && npm run build

# Configura as permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copia as configurações do Nginx e Supervisor
COPY .fly/nginx.conf /etc/nginx/sites-available/default
COPY .fly/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copia e executa o script de inicialização
COPY .fly/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]