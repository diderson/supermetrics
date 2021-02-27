# Supermetrics Assignment

This project have been completed from scratch by using PHP and can run in docker container

you can demo at: https://supermetrics.dipatek.net

## How to run on local
You have two options to run this project using normal php setup or docker


### Local Development with Docker

- Docker CE v17.12.0+, installation guides for [Ubuntu](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/) and [Mac](https://docs.docker.com/docker-for-mac/install/)
- Docker compose [installation guide](https://docs.docker.com/compose/install/)

#### Setup

- Clone the project
- point yourself inside src folder: cd src
- run: composer install
- run: cd ..
- from root folder (supermetrics) run: 
```sh
docker-compose up -d or docker-compose up to monitor the containers
```
- go to: http://localhost:8084


### Local Development without Docker

#### Requirements

- php >=7.3
- composer

#### Setup

- Clone the project
- point yourself inside src folder: cd src
- run: composer install
- then that's it you can try your app


## Generale Note

### Package used

- https://packagist.org/packages/guzzlehttp/guzzle (makes it easy to do http request)
- https://packagist.org/packages/spatie/laravel-collection-macros (I used it to manipulate collection of data from Json)
- https://carbon.nesbot.com/ (been used to work with date)
- https://phpunit.readthedocs.io/en/9.5/installation.html (unit test)

### Unit Test

You can run the unit test from your terminal into folder `src`

run: ./vendor/bin/phpunit --testdox tests

