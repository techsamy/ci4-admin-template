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