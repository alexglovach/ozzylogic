## Тестовое задание Ozzylogic LTD
Сделано в соответствии к данному ТЗ https://docs.google.com/document/d/1kW_FRP38ij1u9lnZ2vUxRstnbzh6ooyNSXJIPFu31F0/edit

## Инструкция по запуску

Проект реализован на Docker.
Перед запуском контейнера нужно запустить установку пакетов composer
``docker run --rm -v $(pwd):/app composer install``

Для запуска контейнера использовать команду
``docker-compose up -d``.

Актуальные конфиги Laravel находятся в файле ``.env.example``.
Для создания файла конфигурации используйте команду ``cp .env.example .env`` в папке проекта.

После этого генерируем ключ 

``docker-compose exec app php artisan key:generate``

и кешируем настройки 

``docker-compose exec app php artisan config:cache``

Теперь проект доступен по адресу ``http://localhost``

Дальше продолжаем установку в соответствии к документации Laravel
