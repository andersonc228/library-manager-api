# Library Manager API

API REST para la gestión de libros con integración OpenLibrary, MySQL para la base de datos, ejecutable en Docker.

## Levantar el proyecto

Levantar contenedores:

```bash
docker-compose build
docker-compose up -d
```

## Entrar al contenedor e installar dependencias
```
docker exec -it library-manager-api-app bash
composer install
exit
```

## Ejecutar Migraciones
```
docker exec -i library-manager-api-db mysql -uroot -proot books_db < migrations/create_books_table.sql
docker exec -i library-manager-api-db mysql -uroot -proot books_db < migrations/create_books_table_test.sql
docker exec -i library-manager-api-db mysql -uroot -proot books_db < migrations/create_users_table.sql
```
## Ejecutar test
```
docker exec -it library-manager-api-app bash
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/
exit
```
## Hay una imagen del resultado de los test en storage/tests.png

## Usuario de prueba:
```
Email: admin@example.com
Password: admin123
```

## URLS de prueba
```
Todos los endpoints de libros requieren el header:
Authorization: Bearer <JWT_TOKEN>


POST /auth/login
{
    "email": "admin@example.com",
    "password": "admin123"
}

Response 200 OK
{
    "data": {
        "token": "token"
    }
}

GET /books
GET /books?q=Orwell
Query param opcional: q para búsqueda por título.

Response 200 OK
{
    "data": [
        {
            "id": 1,
            "title": "1984",
            "author": "George Orwell",
            "isbn": "0451526538",
            "year": 1949,
            "description": "Distopía política sobre vigilancia y control social.",
            "cover_url": null
        }
    ]
}

POST /books
Body JSON:
{
    "title": "Neuromancer",
    "author": "William Gibson",
    "isbn": "0441569595",
    "year": 1984
}
Response 201 Created
{
    "data": {
        "id": 6,
        "title": "Neuromancer",
        "author": "William Gibson",
        "isbn": "0441569595",
        "year": 1984,
        "description": description from OpenLibrary,
        "cover_url": cover_url from OpenLibrary
    }
}

GET /books/{id}
Response 200 OK
{
    "data": {
        "id": 3,
        "title": "To Kill a Mockingbird",
        "author": "Harper Lee",
        "isbn": "9780060935467",
        "year": 1960,
        "description": "Clásico sobre justicia y racismo en el sur de EE. UU.",
        "cover_url": null
    }
}

PATCH /books/{id}
Body JSON:
{
    "year": 1900
}
Response 200 OK
{
    "data": {
        "id": 6,
        "title": "Neuromancer",
        "author": "William Gibson",
        "isbn": "0441569595",
        "year": 1900,
        "description": description from OpenLibrary,
        "cover_url": cover_url from OpenLibrary
    }
}

PUT /books/{id}
Body JSON:
{
    "title": "Neuromancer (Updated)",
    "author": "William Gibson",
    "isbn": "0441569595",
    "year": 1985
}
Response 200 OK
{
    "data": {
        "id": 6,
        "title": "Neuromancer (Updated)",
        "author": "William Gibson",
        "isbn": "0441569595",
        "year": 1985,
        "description": description from OpenLibrary,
        "cover_url": cover_url from OpenLibrary
    }
}

DELETE /books/{id}
Response 200 OK
{
    "data": {
        "success": true
    }
}




