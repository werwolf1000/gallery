FROM php:8.4-fpm as development

ARG NVM_VERSION=v0.40.2
ARG NODE_VERSION=v26.1.0
ARG DEV_USER=1000

# Установка системных зависимостей и PHP‑расширений
RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    mariadb-client \
    unzip \
    librabbitmq-dev \
    iputils-ping \
    && rm -rf /var/lib/apt/lists/*

# Конфигурация и установка PHP‑расширений
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli sockets pgsql pdo_pgsql xml zip opcache soap

# Установка Xdebug (совместим с PHP 8.4)
RUN pecl install xdebug-3.4.1 \
    && docker-php-ext-enable xdebug

# Установка AMQP
RUN pecl install amqp \
    && docker-php-ext-enable amqp

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создание пользователей
RUN groupadd -g 101 nginx \
    && useradd -u 101 -ms /bin/bash -g nginx nginx \
    && useradd -ms /bin/bash dev -u $DEV_USER \
    && usermod -aG nginx dev

# Установка NVM и Node.js для пользователя dev
ENV NVM_DIR /home/dev/.nvm

# Установка NVM
RUN mkdir -p $NVM_DIR \
    && curl -o- https://raw.githubusercontent.com/creationix/nvm/${NVM_VERSION}/install.sh | bash

# Настройка окружения для NVM и установка Node.js
RUN /bin/bash -c "source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default"

# Экспорт переменных окружения для Node.js
ENV NODE_PATH $NVM_DIR/versions/node/$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/$NODE_VERSION/bin:$PATH

# Настройка .bashrc для dev
RUN echo 'export NVM_DIR="$HOME/.nvm"' >> /home/dev/.bashrc \
    && echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> /home/dev/.bashrc \
    && echo '[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"' >> /home/dev/.bashrc \
    && echo 'export PATH="$NVM_DIR/versions/node/'"$NODE_VERSION"'/bin:$PATH"' >> /home/dev/.bashrc

# Убеждаемся, что права на NVM правильные
RUN chown -R dev:dev $NVM_DIR

# Создание директории для дампов
RUN mkdir -p /var/dump/ \
    && chmod -R 0777 /var/dump/

# Переключение на пользователя dev для безопасности (опционально)
# USER dev
