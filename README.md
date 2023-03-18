# Column change bug evidence

A way to easy reproduce a migration bug described here: https://github.com/laravel/framework/issues/46492

## Install 

1. Copy `.env.example` to `.env`
2. docker-compose build
3. docker-compose exec php bash
4. composer install
5. php artisan migrate:fresh

In `database/migrations`. Migration `2023_03_16_175648_original_table.php` sets up three text fields.

Migration `2023_03_16_175653_updated_table.php` attempts to change those columns to `meduimtext` and `longtext` columns.

`vendor/laravel/framework/src/Illuminate/Database/Schema/Grammars/ChangeColumn.php` Creates a comparison between the tables. Fields are set as the `text` enum in `DBAL` and lengths are changed correctly.

However, when passed to `Doctrine\DBAL\Schema\Comparator` on line `64`, no change is detected so the database does not update.

