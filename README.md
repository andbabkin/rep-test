# Real Estate Properties
Test task

## Installation
- git clone
- cp .env .env.local (edit if needed)
- cd .docker
- build containers
```
> DOCKER_BUILDKIT=1 docker-compose up -d --build
```
- go inside php container
```
> docker-compose exec rep-php bash
```
- install dependencies and database structure
```
> composer install
> sf doctrine:migrations:migrate
```
- open `http://localhost:8080/` in browser. It should show the Symfony welcome page
> NB! Probably need to solve permission issues if run on linux system

## Endpoints
### Get property tree
REST API endpoint to retrieve full property tree
```http request
GET http://localhost:8080/props
Accept: application/json
```

### Get property data
REST API endpoint to retrieve selected property
```http request
GET http://localhost:8080/prop/{id}
Accept: application/json
```

### Add property
REST API endpoint to add a new property to any level of a tree.
```http request
POST http://localhost:8080/prop
Accept: application/json
Content-Type: application/json

{
  "name": "Property name",
  "parent": "Parent name"
}
```
