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

## TO create a controller for user functionalities
php spark make:filter CIFilter

## TO create a migration for creating password reset tokens table
php spark make:migration create create_password_reset_tokens_table

## To create the password ResetToken model
php spark make:model PasswordResetToken

## After create migration file, run the migration to create the table
php spark migrate

## To install Carbon for date and time handling
composer require nesbot/carbon

## To create a validation rule for strong passwords
php spark make:validation isPasswordStrong

#8. Setting Up a Profile Page in CodeIgniter 4
Next watch : https://youtu.be/e9IdGbageXA?list=PLX4adOBVJXavmNeP7CU295sX76jgzziio&t=820