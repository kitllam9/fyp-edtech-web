# Installation

1. Install Laravel and NPM on your environment. (https://laravel.com/docs/12.x#installing-php)

2. Host the project's source code.
- One of the options is using XAMPP. (https://www.apachefriends.org)
- After installing XAMPP, put the project's source code or clone its GitHub repository into the ./xampp/htdocs directory.
- Start the Apache and MySQL servers.
- Create a .env file by renaming or duplicating the .env.example.
- Make sure the environment variables containing the database credentials (prefixed with `DB_`) match with one of the databases in the server, otherwise, modify the variables or create a new database.</br></br>
- Run the following commands in the project's directory to acquire all external packages:</br></br>
    ```
    composer install
    npm i
    ```
- Run the following commands in the project's directory for migrations and compiling frontend resources: </br>
    </br>If the `migrations` table did not already exist, first run:</br></br>
    ```
    php artisan migrate:install
    ```
    Then:</br></br>
    ```
    php artisan storage:link
    php artisan migrate
    npm run build
    ```
- And finally, start hosting the application by running `php artisan serve`.

Note: you may encounter permission issues if the project is hosted on MacOS. Please make sure you have granted the directory read/write permissions for creating PDF files in that case.  

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
