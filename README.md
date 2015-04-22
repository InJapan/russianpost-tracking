Трекинг отправлений Почты России из PHP
=======================================

**Changelog**

- *2015-04-22* Информация о наложенном платеже; совместимость с composer
- *2012-11-24* Первая версия

**Инсталляция**

Добавьте в composer.json репозиторий и пакет:

```javascript
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/InJapan/russianpost-tracking"
    }
  ],
  "require": {
    "injapan/russianpost": "dev-master"
  }
}
```

Выполните composer install

**Доступ**

Веб-сервис, к которому обращается библиотека, не требует аутентификации.
Тем не менее, вы обязаны получить разрешение на его использование,
для этого нужно отправить запрос в свободной форме по адресу fc@russianpost.ru.

**Использование**

См. example.php

**Зависимости библиотеки**

Для корректной работы требуется PHP >5.0 с включенными модулями:
- pcre
- curl
- SimpleXML


Russian Post items tracking via PHP
===================================

**Changelog**

- *2015-04-22* COD payment information; composer compatibility
- *2012-11-24* First release

**Install**

Add custom repo and package requirement to composer.json:

```javascript
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/InJapan/russianpost-tracking"
    }
  ],
  "require": {
    "injapan/russianpost": "dev-master"
  }
}
```

Run composer install.

**Access**

Web service used by the library does not require authentication of the requests.
However you're required to obtain authorization for it's usage by mailing freeformed request to fc@russianpost.ru.

**Usage**

See example.php

**Dependencies**

PHP >5.0 is required with following extensions enabled:
- pcre
- curl
- SimpleXML
