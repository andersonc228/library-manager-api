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
docker exec -it library-manager-api_app bash
composer install
exit
```

## Ejecutar Migraciones
```
docker exec -i library-manager-api_db mysql -uroot -proot books_db < migrations/create_books_table.sql
docker exec -i library-manager-api_db mysql -uroot -proot books_db < migrations/create_books_table_test.sql
docker exec -i library-manager-api_db mysql -uroot -proot books_db < migrations/create_users_table.sql
```

## Usuario de prueba:
```
Email: admin@example.com
Password: admin123
```

## Ejecutar test
```
docker exec -it library-manager-api_app bash
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/
exit
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
    "token": "token"
}

GET /books
Query param opcional: q para búsqueda por título.

{
  "data": [
    {
      "id": 1,
      "title": "Clean Code",
      "author": "Robert C. Martin",
      "isbn": "9780132350884",
      "year": 2008,
      "description": "Best practices for writing clean code.",
      "cover_url": "http://example.com/cleancode.jpg"
    }
  ]
}

POST /books
Body JSON:
{
  "title": "Clean Code",
  "author": "Robert C. Martin",
  "isbn": "9780132350884",
  "year": 2008,
  "description": "Best practices for writing clean code.",
  "cover_url": "http://example.com/cleancode.jpg"
}

{
  "data": {
    "id": 1,
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "isbn": "9780132350884",
    "year": 2008,
    "description": "Best practices for writing clean code.",
    "cover_url": "http://example.com/cleancode.jpg"
  }
}

GET /books/{id}
{
  "data": {
    "id": 1,
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "isbn": "9780132350884",
    "year": 2008,
    "description": "Best practices for writing clean code.",
    "cover_url": "http://example.com/cleancode.jpg"
  }
}

PUT /books/{id}
Body JSON (parcial):
{
  "title": "Clean Code (Updated)"
}

DELETE /books/{id}
{
  "status": "success"
}




