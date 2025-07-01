# Usa a imagem base do Ubuntu
FROM ubuntu:jammy

# Define o frontend como não-interativo para evitar perguntas durante a instalação
ENV DEBIAN_FRONTEND=noninteractive

# Instala todas as dependências de sistema (PHP, Nginx, Node, Git, Zip, SQLite) em uma única camada
RUN apt-get update && apt-get install -y \
    software-properties-common curl nginx supervisor git zip unzip \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update && apt-get install -y \
    php8.2 php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-fpm php8.2-zip php8.2-sqlite3 \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do PHP
RUN composer install --optimize-autoloader --no-dev

# Instala as dependências de front-end e compila os assets
RUN npm ci && npm run build

# Configura as permissões
RUN chown -R www-data:www-data storage bootstrap/cache

# Copia os arquivos de configuração
COPY .fly/nginx.conf /etc/nginx/sites-available/default
COPY .fly/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .fly/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expõe a porta e define o ponto de entrada
EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]