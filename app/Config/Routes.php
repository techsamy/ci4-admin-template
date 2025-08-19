<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('admin', static function ($routes) {

    $routes->group('', ['filter' => 'cifilter:auth'], static function ($routes) {
        // $routes->view('example-page', 'example-page');
        $routes->get('home', 'AdminController::index', ['as' => 'admin.home']);
        $routes->get('logout', 'AdminController::logoutHandler', ['as' => 'admin.logout']);

        // Profile
        $routes->get('profile', 'AdminController::profile', ['as' => 'admin.profile']);
        $routes->post('update-personal-details', 'AdminController::updatePersonalDetails', ['as' => 'update-personal-details']);
        $routes->post('update-profile-picture', 'AdminController::updateProfilePicture', ['as' => 'update-profile-picture']);
        $routes->post('change-password', 'AdminController::changePassword', ['as' => 'change-password']);
        $routes->get('settings','AdminController::settings', ['as' => 'settings']);
        $routes->post('update-general-settings', 'AdminController::updateGeneralSettings', ['as' => 'update-general-settings']);
        $routes->post('update-website-logo', 'AdminController::updateWebsiteLogo', ['as' => 'update-website-logo']);
    });

    $routes->group('', ['filter' => 'cifilter:guest'], static function ($routes) {
        // $routes->view('example-auth', 'example-auth');

        // Login
        $routes->get('login', 'AuthController::loginForm', ['as' => 'admin.login.form']);
        $routes->post('login', 'AuthController::loginHandler', ['as' => 'admin.login.handler']);

        // Forgot Password
        $routes->get('forgot-password', 'AuthController::forgotPasswordForm', ['as' => 'admin.forgot.password.form']);
        $routes->post('send-password-reset-link', 'AuthController::sendPasswordResetLink', ['as' => 'send_password_reset_link']);
        $routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1', ['as' => 'admin.reset-password']);
        $routes->post('reset-password-handler/(:any)', 'AuthController::resetPasswordHandler/$1', ['as' => 'reset-password-handler']);
    });
});
