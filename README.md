# Installation

1. Install Laravel and NPM on your environment. (https://laravel.com/docs/12.x#installing-php)

2. Host the project's source code.
- One of the options is using XAMPP. (https://www.apachefriends.org)
- After installing XAMPP, put the project's source code or clone its GitHub repository into the ./xampp/htdocs directory.
- Start the Apache and MySQL servers.
- Create a .env file by renaming or duplicating the .env.example.
- Make sure the environment variables containing the database credentials (prefixed with `DB_`) match with one of the databases in the server, otherwise, modify the variables or create a new database.
- Run the following commands in the project's directory to acquire all external packages:
    ```
    composer install
    npm i
    ```
- Run the following commands in the project's directory for migrations and compiling frontend resources: </br>
    If the `migrations` table did not already exist, first run:
    ```
    php artisan migrate:install
    ```
    Then:
    ```
    php artisan migrate
    npm run build
    ```
- And finally, start hosting the application by running `php artisan serve`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
