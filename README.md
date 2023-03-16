# Column change bug evidence

A way to easy reproduce a migration bug described here: https://github.com/laravel/framework/issues/46492

## Install 

1. Copy `.env.example` to `.env`
2. docker-compose build
3. docker-compose exec php bash
4. composer install
5. php artisan migrate:fresh

Bingo
