# PHP_lesson_high_level

- composer update - обновляем библиотеки;
- composer install - устанавливаем все необходимые библиотеки прописанные в файле composer.json в папку vendor;
- Для перестройки автозагрузчика запускаем команду composer dump-autoload;
- устанавливаем базу данных SQLite;
- подключаем базу данных к проекту;
- устанавливаем расширение pdo_sqlite;
- 
- для работы программы запускаем сервер php -S localhost:8000 http.php
- 
- что умеет приложение:
- поиск пользователя  по username через GET запрос от клиента с передачей query параметров:
- GET http://localhost:8000/users/show?username=ivan123


- создание пользователя через POST запрос с передачей информации о пользователе в теле запроса:
- POST http://localhost:8000/users/create
{
"username": "55547",
"first_name": "ivan",
"last_name": "Ivanov",
"password": 123
}

- авторизация пользователя в системе:
- POST http://localhost:8000/login

{
"username":"ivan123",
"password": 123
}

- создание поста через POST запрос с передачей токена об авторизованном пользователе в header
- и информации о посте в теле запроса:

POST http://localhost:8000/posts/create
Authorization: Bearer 124c732f8b3abacb00d573919c2bc606a9db57fd3caffcea384b559e1bee116b5599fe372be4c5a3

{
"author_uuid": "58e0532f-dbd9-4977-824a-2c58f601ef2c",
"text": "text",
"title": "title"
}

-удаление поста через POST запрос с передачей информации о посте в теле запроса:

POST http://localhost:8000/posts/delete

{
"uuid": "7e8b65eb-0c8f-4c63-8aea-5bc12e74a5c7"
}

-создание лайка посту авторизованным пользователем:

POST http://localhost:8000//postLikes/create
Authorization: Bearer d4d1d14e8640e03505008ec7974085d40d58c60af856bf6b7db4d1c486598001092c60bd7aa47272

{
"post_uuid": "d386ed51-221a-4086-88cb-a6e3070f0a69",
"author_uuid": "b886dff0-6282-43cf-90d3-62be20e53022"
}

создание лайка у комментария к посту авторизованным пользователем:

POST http://localhost:8000//commentLikes/create
Authorization: Bearer d4d1d14e8640e03505008ec7974085d40d58c60af856bf6b7db4d1c486598001092c60bd7aa47272

{
"comment_uuid": "5e4367fc-f944-4579-ad75-73f861c6b716",
"author_uuid": "b886dff0-6282-43cf-90d3-62be20e53022"
}

для запуска тестов необходимо установить библиотеку PHPUnit
 - composer require --dev phpunit/phpunit
 - composer test - команда запуска тестов



