# Структура БД


### Здание

    {
        "_id" : ObjectId("567524b58ffafd489b31124c"),
        "address" : "Россия, Новосибирск, улица Блюхера 32/1",
        "loc" : [ 
            82.9017539999999968, 
            54.9901219999999995
        ]
    }

* *address* - Адрес
* *loc* - Координаты


### Рубрика

    {
        "_id" : ObjectId("56766053751b886f3353455a"),
        "name" : "Шины/Диски",
        "slug" : "tires",
        "parent" : ObjectId("56765fcb751b886f33534558"),
        "ancestors" : [ 
            {
                "_id" : ObjectId("56765e0b751b886f33534553"),
                "name" : "Автомобили",
                "slug" : "cars"
            }, 
            {
                "_id" : ObjectId("56765fcb751b886f33534558"),
                "name" : "Легковые",
                "slug" : "motorcars"
            }
        ]
    }

* *name* - Название
* *slug* - Текстовый идентификатор
* *parent* - Родительская рубрика
* *ancestors* - Массив рубрик, которые являются предками для данной рубрики


### Компания

    {
        "_id" : ObjectId("56765544751b886f3353454e"),
        "name" : "Мега",
        "phones" : [ 
            NumberLong(79130000101)
        ],
        "building" : {
            "_id" : ObjectId("567524b58ffafd489b311250"),
            "address" : "Россия, Новосибирск, улица Ватутина 107",
            "loc" : [ 
                82.9364649999999983, 
                54.9642300000000006
            ]
        },
        "categories" : [ 
            "cars/motorcars/tires", 
            "food"
        ]
    }

* *name* - Название
* *phones* - Массив номеров телефонов
* *building* - Здание, в котором находится компания
* *categories* - Массив рубрик, к которым относится компания
