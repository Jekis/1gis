# Проект 1gis

API для получения инфомрации о компаниях. http://1gis.jekis.koding.io/api/v1/companies

## Требования

1. NGINX
1. MongoDB 3.2
1. PHP 5.5
1. Composer

## Установка

Склонировать репозиторий и перейти в директорию проекта.

    cd /var/www/
    git clone https://github.com/Jekis/1gis.git
    cd 1gis

Пример конфигурации для NGINX

    server {
        listen 80;
        server_name 1gis.loc;
        root /var/www/1gis/web/;
    
        location / {
            # try to serve file directly, fallback to app.php
            try_files $uri /index.php$is_args$args;
        }
    
        location ~ ^/index\.php(/|$) {
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_split_path_info ^(.+\.php)(/.*)$;
            include fastcgi_params;
            fastcgi_param  SCRIPT_FILENAME  $realpath_root$fastcgi_script_name;
            fastcgi_param DOCUMENT_ROOT $realpath_root;
        }
    
        error_log /var/log/nginx/1gis_error.log;
        access_log /var/log/nginx/1gis_access.log;
    }

Установка приложения

    composer install

Далле будет предложено настроить подключение к базе данных. Установите ваши настройки или везде нажмите `Enter`, чтобы установить значение по-умолчанию.

    db_host (localhost):
    db_port (27017):
    db_name (1gis):
    db_user (null):
    db_password (null):

Все параметры можно будет изменить в файле `app/config/parameters.yml`

Создание базы данных из дампа. Будет создана база `1gis`

    mongorestore --gzip --archive=src/App/Resources/dump/1gis.gz --db 1gis
    
## API

* [Структура БД](src/App/Resources/doc/schema.md)
* [Документация](src/App/Resources/doc/api.md)
