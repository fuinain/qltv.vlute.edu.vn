FROM php:8.2-fpm

USER root
WORKDIR /var/www/app

RUN apt-get update && apt-get install -y \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    zip \
    unzip \
    cron \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        gd

# Use the default production configuration for PHP-FPM ($PHP_INI_DIR variable already set by the default image)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Setup php.ini
RUN sed -i -r "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" "$PHP_INI_DIR/php.ini" \
    && sed -i -r "s/expose_php = On/expose_php = Off/g" "$PHP_INI_DIR/php.ini" \
    && sed -i -r "s/post_max_size = 8M/post_max_size = 32M/g" "$PHP_INI_DIR/php.ini" \
    && sed -i -r "s/upload_max_filesize = 2M/upload_max_filesize = 32M/g" "$PHP_INI_DIR/php.ini" \
    && sed -i -r "s/session.gc_maxlifetime = 1440/session.gc_maxlifetime = 86401/g" "$PHP_INI_DIR/php.ini" \
    && sed -i -r "s/session.gc_maxlifetime = 1440/session.gc_maxlifetime = 86401/g" "$PHP_INI_DIR/php.ini" \
    && sed -i -r "s/max_execution_time = 30/max_execution_time = 180/g" "$PHP_INI_DIR/php.ini"

# Setup OPcache
RUN echo "opcache.enable = 1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.enable_cli = 1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.memory_consumption = 512" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.interned_strings_buffer = 8" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.max_accelerated_files = 50000" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.revalidate_freq = 5" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.save_comments = 0" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.enable_file_override = 1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.huge_code_pages = 0" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini" \
    && echo "opcache.fast_shutdown = 1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini"

# Insert before [www] directive
RUN sed -i '0,/^\[www\].*/s/^\[www\].*/emergency_restart_threshold = 10\n&/' "/usr/local/etc/php-fpm.d/docker.conf" \
    && sed -i '0,/^\[www\].*/s/^\[www\].*/emergency_restart_interval = 1m\n&/' "/usr/local/etc/php-fpm.d/docker.conf" \
    && sed -i '0,/^\[www\].*/s/^\[www\].*/process_control_timeout = 5\n&/' "/usr/local/etc/php-fpm.d/docker.conf"

# Setup FPM `www` (default) pool
RUN sed -i -r "s/pm.max_children = 5/pm.max_children = 640/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/pm.start_servers = 2/pm.start_servers = 18/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/pm.min_spare_servers = 1/pm.min_spare_servers = 12/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/pm.max_spare_servers = 3/pm.max_spare_servers = 24/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/;pm.process_idle_timeout = 10s/pm.process_idle_timeout = 10s/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/;pm.max_requests = 500/pm.max_requests = 500/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/;pm.status_path/pm.status_path/g" "/usr/local/etc/php-fpm.d/www.conf" \
    && sed -i -r "s/max_execution_time = 30/max_execution_time = 180/g" "/usr/local/etc/php-fpm.d/www.conf"

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN ln -sf /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime
RUN echo "Asia/Ho_Chi_Minh" > /etc/timezone

# Copy entrypoint script
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Đặt entrypoint mới
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
