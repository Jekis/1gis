# Компании

## Получить компании 

    GET /api/v1/companies

[Пример ответа](json/companies.json.md)

### Параметры запроса

| Наименование | Тип | Пример значения | По-умолчанию | Описание |
|:-------------|:----|:----------------|:-------------|:---------|
| limit |  integer | 10 | 50 | Количество элементов, которое будет выдано. Максимальное количесетво ограничевается значением 50
| offset | integer | 10 | 0 | Смещение. Какое кол-во элементов пропустить
| lng | float | 82.898484 | нет | Координата долготы центра поиска (радианы)
| lat | float | 54.9882 | нет | Координата широты центра поиска (радианы)
| radius | float | 1 | нет | Радиус поиска в км
| category | string | cars/trucks | нет | Категория

**Примеры:**

Получить все компании, выдать только 10 в ответе.

    GET /api/v1/companies?limit=10

Получить следующие 10 компаний.

    GET /api/v1/companies?limit=10&offset=10

Получить компании в радиусе 4км от точки [82.896588, 54.987358]. Обязательны три параметра `lng`, `lat` и `radius`

    GET /api/v1/companies?lng=82.896588&lat=54.987358&radius=4

Получить компании рубрики. У каждой рубрики есть поле `slug`, которое и следует передавать в этом параметре. Допустим дерево такое:

    cars
        trucks
            tires
            ..
        motorcars
            tires
            ..
        ..
    food
    ..

Получить все компании рубрики `cars`

    GET /api/v1/companies?category=/cars

Получить все компании рубрик `tires` (trucks) и `tires` (motorcars)

    GET /api/v1/companies?category=tires

Получить все компании только рубрики `tires` (motorcars)

    GET /api/v1/companies?category=motorcars/tires
    
или

    GET /api/v1/companies?category=/cars/motorcars/tires


## Получить все компании здания

    GET /api/v1/buildings/{buildingId}/companies

[Пример ответа](json/companies2.json.md)

Получить все компании здания

    GET /api/v1/buildings/567524b58ffafd489b31124b/companies
