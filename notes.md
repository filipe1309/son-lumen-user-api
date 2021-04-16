# DevOnTheRun Notes

> notes taken during the course

https://github.com/barryvdh/laravel-ide-helper#usage-with-lumen

php artisan make:migration create_users_table

php artisan migrate

php artisan make:seeder UsersTableSeeder

php artisan db:seed

POST http://localhost:5001/api/users
{
"email": "test3@test.com",
"name": "Testerson2",
"password": "123456",
"password_confirmation": "1234561"
}

POST http://localhost:5001/api/login
{
"email": "test@test.com",
"password": "123456",
}

php artisan make:migration add_api_token_to_users --table=users
