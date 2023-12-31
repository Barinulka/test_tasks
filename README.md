# test_tasks

Задание разделил на несколько веток

>Теоретическая часть 
```
main
```
>Практичекая часть
```
practice_same_color - Первое задание "Клетки шахматной доски"
practice_parse - Второе задание "Недоимки и задолженности"
```

Для запуска второго задания нужно:

1. Клонировать репозиторий
```
git clone https://github.com/Barinulka/test_tasks.git
```
2. Переключиться на ветку задания
```
git checkout origin/practice_parse
```
3. Проинициализировать composer
```
composer init
```
4. Если не сгенерировался файл vendor/autoload.php
```
composer dump-autoload
```
5. Настроить подкючение к базе в файле
```
/database/config.php
```
6. Файлы миграций лежат
```
/database/migrations/
```
7. Применить все доступные миграции
```
php migrate.php
```
8. Работа с парсером
> В каталоге /storage/ лежат xml файлы для парсера

> Поверка на наличе новых данных на сайте
```
php check.php check
```
> Загрузка и распаковка нового файла
```
php check.php load
```
> Запуск парсинга файлов в БД
```
php app.php parse 
```
> Вывести список компаний с наибольшей суммарной задолженностью
```
php app.php totalDebt 
```
> Вывести общую задолженность всех компаний по каждому виду налога
```
php app.php totalTaxName 
```
> Вывести среднюю задолженность по регионам
```
php app.php totalAvg 
```
