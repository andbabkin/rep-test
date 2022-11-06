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
