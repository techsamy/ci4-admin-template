## TO create migration file for creating users table
php spark make:migration create_users_table

## TO create this table in the database
php spark migrate

## TO refresh the migrations and recreate the tables
php spark migrate:refresh

## TO create a model for the users table
php spark make:model user

## TO create a seeder for the users table
php spark make:seeder UserSeeder

## TO run the seeder to insert initial data into the users table
php spark db:seed UserSeeder

## TO create a controller for authentication
php spark make:controller AuthController

## TO create a controller for admin functionalities
php spark make:controller AdminController


Next watch : https://youtu.be/1YWrEmBjEts?list=PLX4adOBVJXavmNeP7CU295sX76jgzziio